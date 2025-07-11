from flask import Flask, request, jsonify
import numpy as np
import joblib
import traceback  # Import traceback for detailed error logs

app = Flask(__name__)

# Load trained model and encoders
try:
    model = joblib.load("flight_model.pkl")
    departure_encoder = joblib.load("departure_encoder.pkl")
    arrival_encoder = joblib.load("arrival_encoder.pkl")
    airline_encoder = joblib.load("airline_encoder.pkl")
except Exception as e:
    print("Model or Encoder Loading Error:", str(e))
    exit(1)  # Stop execution if models are missing

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()
        print("Received Data:", data)  # Debugging

        # Encode categorical inputs
        departure_encoded = departure_encoder.transform([data["departure_location"]])[0]
        arrival_encoded = arrival_encoder.transform([data["arrival_location"]])[0]
        airline_encoded = airline_encoder.transform([data["airline"]])[0]

        # Convert time (HH:MM) to minutes since midnight
        try:
            hours, minutes = map(int, data["departure_time"].split(":"))
            departure_time = hours * 60 + minutes
        except Exception as e:
            print("Time Conversion Error:", str(e))
            return jsonify({"error": "Invalid time format. Expected HH:MM."}), 400

        # Prepare input features
        features = np.array([[departure_encoded, arrival_encoded, departure_time, airline_encoded]])

        # Make prediction
        prediction = model.predict(features)[0]

        return jsonify({"predicted_status": prediction})

    except Exception as e:
        print("Prediction Error:", str(e))
        print(traceback.format_exc())  # Print full error stack trace
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)