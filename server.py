import json
from http.server import BaseHTTPRequestHandler, HTTPServer
import stripe
import mysql.connector
from urllib.parse import urlparse, parse_qs

# ========== НАСТРОЙКИ ==========
STRIPE_SECRET_KEY = "sk_test_ВАШ_SECRET_KEY"
PORT = 5000

DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "ТВОЙ_ПАРОЛЬ",
    "database": "rentit"
}
# ===============================

stripe.api_key = STRIPE_SECRET_KEY

# Подключение к БД
conn = mysql.connector.connect(**DB_CONFIG)
cursor = conn.cursor(dictionary=True)

# Убедимся, что есть таблица users
cursor.execute("""
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255),
    balance_cents INT DEFAULT 0
)
""")
conn.commit()

# Добавим тестового пользователя (id=1), если нет
cursor.execute("SELECT id FROM users WHERE id=1")
if cursor.fetchone() is None:
    cursor.execute("INSERT INTO users (id, email, balance_cents) VALUES (1, 'test@example.com', 0)")
    conn.commit()

class Handler(BaseHTTPRequestHandler):
    def _send_json(self, code, obj):
        self.send_response(code)
        self.send_header("Content-Type", "application/json")
        self.send_header("Access-Control-Allow-Origin", "*")  # CORS для тестов
        self.end_headers()
        self.wfile.write(json.dumps(obj).encode())

    def do_OPTIONS(self):
        self.send_response(200)
        self.send_header("Access-Control-Allow-Origin", "*")
        self.send_header("Access-Control-Allow-Methods", "GET,POST,OPTIONS")
        self.send_header("Access-Control-Allow-Headers", "Content-Type")
        self.end_headers()

    def do_POST(self):
        parsed = urlparse(self.path)

        if parsed.path == "/create-checkout-session":
            length = int(self.headers.get("Content-Length", 0))
            body = self.rfile.read(length)
            data = json.loads(body.decode() or "{}")
            amount_cents = data.get("amount_cents", 1000)

            try:
                session = stripe.checkout.Session.create(
                    payment_method_types=["card"],
                    mode="payment",
                    line_items=[{
                        "price_data": {
                            "currency": "eur",
                            "product_data": {"name": "RentIT - Balance Top-up"},
                            "unit_amount": amount_cents,
                        },
                        "quantity": 1,
                    }],
                    success_url="http://localhost:3000/success.html",
                    cancel_url="http://localhost:3000/cancel.html",
                )
                self._send_json(200, {"id": session.id})
            except Exception as e:
                self._send_json(400, {"error": str(e)})

        elif parsed.path == "/webhook":
            length = int(self.headers.get("Content-Length", 0))
            body = self.rfile.read(length)
            try:
                event = json.loads(body.decode())
            except Exception:
                self._send_json(400, {"error": "invalid json"})
                return

            if event.get("type") == "checkout.session.completed":
                session = event.get("data", {}).get("object", {})
                amount = session.get("amount_total") or 0
                if amount:
                    cursor.execute("UPDATE users SET balance_cents = balance_cents + %s WHERE id=%s", (amount, 1))
                    conn.commit()
                    print(f"User 1 balance increased by {amount} cents")
            self._send_json(200, {"received": True})

        else:
            self._send_json(404, {"error": "not found"})

    def do_GET(self):
        parsed = urlparse(self.path)

        if parsed.path == "/balance":
            qs = parse_qs(parsed.query)
            user_id = int(qs.get("user_id", ["1"])[0])
            cursor.execute("SELECT balance_cents FROM users WHERE id=%s", (user_id,))
            row = cursor.fetchone()
            bal = row["balance_cents"] if row else 0
            self._send_json(200, {"balance": bal})
        else:
            self._send_json(404, {"error": "not found"})

if __name__ == "__main__":
    print(f"Starting server on http://localhost:{PORT}")
    httpd = HTTPServer(("localhost", PORT), Handler)
    httpd.serve_forever()
