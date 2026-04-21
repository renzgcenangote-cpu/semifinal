import serial
import requests
import time

# Update COM port to match yours
try:
    arduino = serial.Serial(port='COM8', baudrate=9600, timeout=1)
    print("--- Bridge Active: Waiting for Scans ---")
except:
    print("ERROR: Could not connect to Arduino. Check COM port.")
    exit()

while True:
    if arduino.in_waiting > 0:
        raw_data = arduino.readline().decode('utf-8', errors='ignore').strip()
        
        if raw_data:
            # Remove all spaces and hidden characters for the URL
            clean_uid = "".join(raw_data.split())
            print(f"\n[SCAN] Raw: {raw_data} -> Clean: {clean_uid}")
            
            try:
                url = f"http://localhost/attendance_system/rfid_handler.php?uid={clean_uid}"
                response = requests.get(url)
                
                # Split the server response for better reading
                result = response.text.split('|')
                
                if result[0] == "SUCCESS":
                    print(f"✅ {result[1]}: {result[2]}")
                elif result[0] == "NOT_FOUND":
                    print(f"❌ ERROR: Card not in Database. {result[1]}")
                else:
                    print(f"⚠️ SERVER: {response.text}")
                    
            except Exception as e:
                print(f"🌐 Connection Error: {e}")
                
    time.sleep(0.1)