import mysql.connector

# Параметры подключения
config = {
    'host': 'localhost',    # или '127.0.0.1'
    'port' : 3306,
    'user': 'root',         # пользователь
    'password': '',         # пароль
}

# Создаём подключение
conn = None 
try:
    conn = mysql.connector.connect(**config)
    cursor = conn.cursor()
    print("Подключение к MySQL успешно!")

    # Создание базы, если её нет
    cursor.execute("CREATE DATABASE IF NOT EXISTS my_database")
    print("База данных готова!")

    # Подключаемся уже к конкретной базе
    conn.database = 'my_database'

    # Создание таблицы
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50)
        )
    """)
    print("Таблица users готова!")

except mysql.connector.Error as err:
    print(f"Ошибка: {err}")

finally:
    if conn and conn.is_connected():
        cursor.close()
        conn.close()
        print("Соединение закрыто.")
