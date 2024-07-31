import os
import pickle
import joblib
from flask import Flask, request, render_template, redirect, jsonify, url_for, session
import pandas as pd
from sklearn.preprocessing import StandardScaler
from werkzeug.security import generate_password_hash, check_password_hash
import mysql.connector  # Add this import for MySQL database

app = Flask(__name__)
app.secret_key = 'your_secret_key'

# Simulated Database
admins = {}
appointments = []
doctors = []

# Database configuration
db_config = {
    'user': 'root',
    'password': '',
    'host': 'localhost',
    'database': 'user_db'
}

# Function to get database connection
def get_db_connection():
    return mysql.connector.connect(**db_config)

@app.route('/admin/login', methods=['GET', 'POST'])
def admin_login():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        if username in admins and check_password_hash(admins[username], password):
            session['admin'] = username
            return redirect(url_for('admin_dashboard'))
    return render_template('admin.html')

@app.route('/admin/register', methods=['POST'])
def admin_register():
    username = request.form['username']
    password = generate_password_hash(request.form['password'])
    admins[username] = password
    return redirect(url_for('admin_login'))

@app.route('/admin/dashboard')
def admin_dashboard():
    if 'admin' not in session:
        return redirect(url_for('admin_login'))
    return render_template('dashboard.html', appointments=appointments, doctors=doctors)

@app.route('/admin/addDoctor', methods=['POST'])
def add_doctor():
    if 'admin' not in session:
        return redirect(url_for('admin_login'))
    doctor_name = request.form['doctorName']
    specialization = request.form['specialization']
    doctors.append({'name': doctor_name, 'specialization': specialization})
    return redirect(url_for('admin_dashboard'))

@app.route('/admin/deleteDoctor/<int:index>', methods=['POST'])
def delete_doctor(index):
    if 'admin' not in session:
        return redirect(url_for('admin_login'))
    if index < len(doctors):
        doctors.pop(index)
    return redirect(url_for('admin_dashboard'))

@app.route('/admin/deleteAppointment/<int:index>', methods=['POST'])
def delete_appointment(index):
    if 'admin' not in session:
        return redirect(url_for('admin_login'))
    if index < len(appointments):
        appointments.pop(index)
    return redirect(url_for('admin_dashboard'))

# Load the pre-trained models and the scaler
with open('models/forest_model.pkl', 'rb') as file:
    forest_model = pickle.load(file)

with open('models/log_model.pkl', 'rb') as file:
    log_model = pickle.load(file)

with open('models/tree_model.pkl', 'rb') as file:
    tree_model = pickle.load(file)

scaler = joblib.load('models/scaler.pkl')

@app.route('/')
def index():
    return render_template('index.html')

# User Login (if you still need it)
@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        # Implement your user authentication logic here
        if username and password == 'password':
            session['user_id'] = username  # You can set a unique user ID here
            return redirect(url_for('index'))
    return render_template('login.html')

@app.route('/logout')
def logout():
    session.pop('user_id', None)
    return redirect(url_for('index'))

@app.route('/upload', methods=['POST'])
def upload():
    file = request.files['file']
    if not file:
        return jsonify({'message': 'No file uploaded', 'prediction': 'N/A'}), 400

    # Save the uploaded file
    file_path = os.path.join('uploads', file.filename)
    file.save(file_path)

    # Read the uploaded file
    if file.filename.endswith('.csv'):
        data = pd.read_csv(file_path)
        prediction = predict_cancer(data)
    else:
        return jsonify({'message': 'An error has been detected in the processing of test results. Please review the reports!!', 'prediction': 'N/A'}), 400

    # Save the prediction and file path to the database
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute("""
        INSERT INTO prediction_records (file_path, prediction_result, created_at)
        VALUES (%s, %s, NOW())
    """, (file_path, prediction))
    conn.commit()
    cursor.close()
    conn.close()

    return jsonify({'message': 'File successfully uploaded', 'prediction': prediction})

def predict_cancer(data):
    # Ensure the data has the correct number of features (drop any unnecessary columns)
    data = data.iloc[:, 2:31]  # Adjust this slice based on your data

    # Scale the data
    scaled_data = scaler.transform(data)

    # Predict using the RandomForest model
    predictions = forest_model.predict(scaled_data)

    # Map numeric predictions back to categorical values
    predictions = ["Malignant" if pred == 1 else "Benign" for pred in predictions]

    # For demonstration, returning the first prediction
    return predictions[0] if predictions else "N/A"

if __name__ == '__main__':
    app.run(debug=True)
