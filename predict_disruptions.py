import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
import pickle

# Load historical flight data
df = pd.read_csv('flights_history.csv')

# Convert categorical variables into numbers
df['departure_location'] = df['departure_location'].astype('category').cat.codes
df['arrival_location'] = df['arrival_location'].astype('category').cat.codes
df['airline'] = df['airline'].astype('category').cat.codes
df['status'] = df['status'].map({'Scheduled': 0, 'Delayed': 1, 'Cancelled': 2})

# Define features and labels
X = df[['departure_location', 'arrival_location', 'departure_time', 'airline']]
y = df['status']

# Split dataset
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Train the model
model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

# Save model
with open('flight_prediction_model.pkl', 'wb') as f:
    pickle.dump(model, f)

print("Model trained and saved successfully!")
