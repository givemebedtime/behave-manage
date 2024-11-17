import hashlib
import time
from flask import Flask, render_template, request
import mysql.connector

# ตั้งค่าเชื่อมต่อฐานข้อมูล
db = mysql.connector.connect(
    host="localhost",
    user="root",  # เปลี่ยนเป็น username ของคุณ
    password="your_password",  # เปลี่ยนเป็น password ของคุณ
    database="blockchain"
)

cursor = db.cursor()

app = Flask(__name__)

# ฟังก์ชันสำหรับสร้าง Chain ใหม่
def create_chain(chain_topic):
    # Chain ID จะถูกสร้างขึ้นจากจำนวน Chain ที่มีอยู่
    cursor.execute("SELECT COUNT(*) FROM block")
    chain_id = cursor.fetchone()[0] + 1  # Chain ใหม่จะมี ID เป็นจำนวน Chain ที่มีอยู่บวก 1

    # สร้าง Genesis Block (Block แรกของ Chain)
    genesis_block_id = f"genesis_{chain_id}"
    genesis_block_index = "0"  # Block แรกมี index 0
    genesis_block_data = "Genesis Block"
    genesis_block_timestamp = int(time.time())
    genesis_block_hash = hashlib.sha256(genesis_block_data.encode('utf-8')).hexdigest()
    previous_hash = "0"  # ไม่มี Previous Hash สำหรับ Genesis Block

    # Insert ข้อมูล Genesis Block ลงฐานข้อมูล
    cursor.execute("""
        INSERT INTO block (Block_ID, Block_INDEX) VALUES (%s, %s)
    """, (genesis_block_id, genesis_block_index))

    cursor.execute("""
        INSERT INTO block_index (Block_INDEX, Block_Data, Block_timeStamp, Hash) 
        VALUES (%s, %s, FROM_UNIXTIME(%s), %s)
    """, (genesis_block_index, genesis_block_data, genesis_block_timestamp, genesis_block_hash))

    cursor.execute("""
        INSERT INTO hash (Hash, Previous_Hash) 
        VALUES (%s, %s)
    """, (genesis_block_hash, previous_hash))

    db.commit()
    return chain_id, genesis_block_index  # Return Chain ID and Genesis Block Index

# ฟังก์ชันสำหรับเพิ่ม Block ใหม่
def add_block(chain_id, block_data, previous_block_index):
    # สร้าง Block ใหม่
    new_block_index = str(int(previous_block_index) + 1)
    new_block_timestamp = int(time.time())
    new_block_hash = hashlib.sha256(block_data.encode('utf-8')).hexdigest()
    previous_hash = get_previous_hash_by_index(previous_block_index)

    # Insert ข้อมูลใหม่ลงฐานข้อมูล
    cursor.execute("""
        INSERT INTO block (Block_ID, Block_INDEX) VALUES (%s, %s)
    """, (f"block_{chain_id}_{new_block_index}", new_block_index))

    cursor.execute("""
        INSERT INTO block_index (Block_INDEX, Block_Data, Block_timeStamp, Hash) 
        VALUES (%s, %s, FROM_UNIXTIME(%s), %s)
    """, (new_block_index, block_data, new_block_timestamp, new_block_hash))

    cursor.execute("""
        INSERT INTO hash (Hash, Previous_Hash) 
        VALUES (%s, %s)
    """, (new_block_hash, previous_hash))

    db.commit()

# ฟังก์ชันในการดึงค่า Previous Hash
def get_previous_hash_by_index(block_index):
    cursor.execute("SELECT Hash FROM block_index WHERE Block_INDEX = %s", (block_index,))
    result = cursor.fetchone()
    return result[0] if result else "0"

# ฟังก์ชันในการค้นหา Block ด้วย Block_INDEX
def search_block(block_index):
    cursor.execute("SELECT * FROM block_index WHERE Block_INDEX = %s", (block_index,))
    block = cursor.fetchone()
    return block

# ฟังก์ชันในการค้นหาหัวข้อของ Chain ที่เกี่ยวข้องกับ Block
def search_chain_by_topic(chain_topic):
    cursor.execute("SELECT Block_INDEX, Block_Data FROM block_index WHERE Block_Data LIKE %s", (f"%{chain_topic}%",))
    blocks = cursor.fetchall()
    return blocks

@app.route('/')
def home():
    return render_template('index.php')

@app.route('/create_chain', methods=['POST'])
def create_new_chain():
    chain_topic = request.form['chain_topic']
    chain_id, genesis_block_index = create_chain(chain_topic)
    return render_template('index.php', message=f"Chain '{chain_topic}' created with Genesis Block.")

@app.route('/add_block', methods=['POST'])
def add_new_block():
    chain_id = int(request.form['chain_id'])
    block_data = request.form['block_data']
    previous_block_index = request.form['previous_block_index']
    add_block(chain_id, block_data, previous_block_index)
    return render_template('index.php', message=f"Block added to Chain {chain_id}.")

@app.route('/search_block', methods=['POST'])
def search_for_block():
    block_index = request.form['block_index']
    block = search_block(block_index)
    return render_template('index.php', block=block)

@app.route('/search_chain', methods=['POST'])
def search_for_chain():
    chain_topic = request.form['chain_topic']
    blocks = search_chain_by_topic(chain_topic)
    return render_template('index.php', blocks=blocks)

if __name__ == '__main__':
    app.run(debug=True)
