<?php
function predictFlightDisruption($departure, $arrival, $time, $airline) {
    $url = "http://127.0.0.1:5000/predict"; // Ensure Flask API URL is correct

    // Convert PHP datetime string to HH:MM format
    $departure_time = date("H:i", strtotime($time));

    $data = array(
        'departure_location' => $departure,
        'arrival_location' => $arrival,
        'departure_time' => $departure_time, // Send HH:MM format
        'airline' => $airline
    );

    // Initialize cURL session
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute request and capture response
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check if response is valid
    if ($http_code == 200 && $response !== false) {
        $result = json_decode($response, true);
        return $result['predicted_status'] ?? "Unknown";
    } else {
        return "Error: API request failed with status $http_code";
    }
}

// Example usage
$predicted_status = predictFlightDisruption("Nairobi", "London", "2025-05-10 14:00:00", "Kenya Airways");
echo "AI Prediction: " . $predicted_status;
?>
