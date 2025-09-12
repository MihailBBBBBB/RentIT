import mysql.connector
conn = mysql.connector (
    host = "10.0.20.17",
    user = "root",
    password = "",
    database = "mydb"
)

cursor = conn.cursor()

cursor.execute("SELECT * FROM users")
for row in cursor.fetchall():
    print(row)

conn.close()