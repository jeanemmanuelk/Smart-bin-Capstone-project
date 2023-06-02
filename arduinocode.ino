#include <WiFi.h>
#include <HTTPClient.h>
#include "HX711.h"

// HX711 circuit wiring
const int LOADCELL_DOUT_PIN = 32;
const int LOADCELL_SCK_PIN = 33;
//distance sensor wiring
const int  trigPin = 2;
const int echoPin = 5;
long duration;
int distance;

HX711 scale;
float weight;

const char* ssid = ""; // your local wifi ssid
const char* password = ""; // wifi password
const char* host = "http://kjesens.xyz/jkouad96/post-esp-data.php"; // hostname

// Keep this API Key value to be compatible with the PHP code provided in the project page. 
// If you change the apiKeyValue value, the PHP file /post-esp-data.php also needs to have the same key 
String apiKeyValue = "tPmAT5Ab3j7F9"; 



void setup()
{
    //setting up the distance sensor  
    pinMode(trigPin, OUTPUT);
    pinMode(echoPin, INPUT);  
    Serial.begin(115200);
    //seting up the scale
    scale.begin(LOADCELL_DOUT_PIN, LOADCELL_SCK_PIN);
    scale.set_scale(11408.59);  // calibration factor for onces(OZ);
    scale.tare();  // reset the scale to 0

    //starting wifi
    WiFi.begin(ssid,password);
    while (WiFi.status()!= WL_CONNECTED){
      delay(500);
      Serial.print(".");
    }
    Serial.println("");
    Serial.println("WiFi connected");
    Serial.println("IP Address");
    Serial.println(WiFi.localIP());
 
}

void loop()
{

  // Clears the trigPin
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  // Sets the trigPin on HIGH state for 10 micro seconds
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);
  // Reads the echoPin, returns the sound wave travel time in microseconds
  duration = pulseIn(echoPin, HIGH);
  // Calculating the distance
  distance= duration*0.034/2;

    
    weight= scale.get_units(); //reading the values from weight sensor
    if (weight < 0) weight = 0.0;

    //checking the sate of the bin; if full or not
    //if full then gets the data sent and display the state full
    if (distance <12 && weight >30.0)
    {
      int binState = 1; //holding the bin state

      //Check WiFi connection status
    if(WiFi.status()== WL_CONNECTED){
      HTTPClient http;
    
    // Your Domain name with URL path or IP address with path
    http.begin(host);
    
    // Specify content-type header
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    //http.addHeader("Content-Type", "application/json");
    
    // Prepare your HTTP POST request data
    String httpRequestData = "api_key=" + apiKeyValue + "&distance=" + String(distance)+ "&weight=" + String(weight)+ "&binState=" + String(binState) + "";
    Serial.print("httpRequestData: ");
    Serial.println(httpRequestData);

    // Send HTTP POST request
    int httpResponseCode = http.POST(httpRequestData);
    
    if (httpResponseCode>0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
    }
    else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
    }
    // Free resources
    http.end();
  }
  else {
    Serial.println("WiFi Disconnected");
  }
  //Send an HTTP POST request every 5 seconds
  delay(5000);
    }
    
 
 //if bin not full, then display state not full
 else 
    {
        int binState = 0; //holding the bin state

        //Check WiFi connection status
    if(WiFi.status()== WL_CONNECTED){
      HTTPClient http;
    
    // Your Domain name with URL path or IP address with path
    http.begin(host);
    
    // Specify content-type header
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    //http.addHeader("Content-Type", "application/json");
    
    // Prepare your HTTP POST request data
    String httpRequestData = "api_key=" + apiKeyValue + "&distance=" + String(distance)+ "&weight=" + String(weight)+ "&binState=" + String(binState) + "";
    Serial.print("httpRequestData: ");
    Serial.println(httpRequestData);

    // Send HTTP POST request
    int httpResponseCode = http.POST(httpRequestData);
    
    if (httpResponseCode>0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
    }
    else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
    }
    // Free resources
    http.end();
  }
  else {
    Serial.println("WiFi Disconnected");
  }
  //Send an HTTP POST request every 5 seconds
  delay(5000);
    }          
}
    
  
    
