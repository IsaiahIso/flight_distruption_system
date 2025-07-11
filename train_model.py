import pandas as pd
import numpy as np
import joblib
from sklearn.preprocessing import LabelEncoder
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier

# Load dataset
df = pd.read_csv("flight_data.csv")  # Update with your dataset

# Encode categorical features
departure_encoder = LabelEncoder()
arrival_encoder = LabelEncoder()
airline_encoder = LabelEncoder()

df["departure_location"] = departure_encoder.fit_transform(df["departure_location"])
df["arrival_location"] = arrival_encoder.fit_transform(df["arrival_location"])
df["airline"] = airline_encoder.fit_transform(df["airline"])

# Convert departure time (HH:MM) to minutes since midnight
df["departure_time"] = df["departure_time"].astype(int)

# Define features and target variable
X = df[["departure_location", "arrival_location", "departure_time", "airline"]]
y = df["delay_status"]  # Adjust to match your dataset column

# Split data into training and test sets
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Train model
model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

# Save trained model and encoders
joblib.dump(model, "flight_model.pkl")
joblib.dump(departure_encoder, "departure_encoder.pkl")
joblib.dump(arrival_encoder, "arrival_encoder.pkl")
joblib.dump(airline_encoder, "airline_encoder.pkl")

print("Model training complete. Encoders and model saved.")
