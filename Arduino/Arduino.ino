// ESP8266 Simple sniffer
// 2018 Carve Systems LLC
// Angel Suarez-B Martin

#include <Arduino.h>
#include <ESP8266WiFi.h>
#include "sdk_structs.h"
#include "ieee80211_structs.h"
#include "string_utils.h"


extern "C"
{
#include "user_interface.h"
}


/******************************************************************************/
/*
char ssid[] = "Wifi-Username";          //  your network SSID (name)
char pass[] = "Wifi-Password";   // your network password

int status = WL_IDLE_STATUS;
char hostGet[] = "google.com"; // remote server we will connect to
const int httpGetPort = 80;

WiFiClient myClient;


void postData() {


  //the path and file to send the data to:
  String urlGet = "/data/collector.php";


  // We now create and add parameters:
  String src = "ESP";
  String typ = "flt";
  String nam = "temp";
  String vint = "92";


  urlGet += "?src=" + src + "&typ=" + typ + "&nam=" + nam + "&int=" + vint;


  status = WiFi.begin(ssid, pass);

  if ( status != WL_CONNECTED) {
    Serial.println("Couldn't get a wifi connection");
    // don't do anything else:
    while (true);
  }
  else {
    Serial.println("Connected to wifi");
    Serial.println("\nStarting connection...");

    if (!myClient.connect(hostGet, httpGetPort)) {
      Serial.print("Connection failed: ");
      Serial.print(hostGet);
    } else {
      myClient.println("GET " + urlGet + " HTTP/1.1");
      myClient.print("Host: ");
      myClient.println(hostGet);
      myClient.println("User-Agent: ESP8266/1.0");
      myClient.println("Connection: close\r\n\r\n");

      unsigned long timeoutP = millis();
      while (myClient.available() == 0) {

        if (millis() - timeoutP > 10000) {
          Serial.print(">>> Client Timeout: ");
          Serial.println(hostGet);
          myClient.stop();
          return;
        }
      }

      //just checks the 1st line of the server response. Could be expanded if needed.
      while (myClient.available()) {
        String retLine = myClient.readStringUntil('\r');
        Serial.println(retLine);
        break;
      }

    } //end client connection if else


    Serial.print(">>> Closing host: ");
    Serial.println(hostGet);
    myClient.stop();

  }





}
*/
/******************************************************************************/
// In this example, the packet handler function does all the parsing and output work.
// This is NOT ideal.
void wifi_sniffer_packet_handler(uint8_t *buff, uint16_t len)
{
  // First layer: type cast the received buffer into our generic SDK structure
  const wifi_promiscuous_pkt_t *ppkt = (wifi_promiscuous_pkt_t *)buff;
  // Second layer: define pointer to where the actual 802.11 packet is within the structure
  const wifi_ieee80211_packet_t *ipkt = (wifi_ieee80211_packet_t *)ppkt->payload;
  // Third layer: define pointers to the 802.11 packet header and payload
  const wifi_ieee80211_mac_hdr_t *hdr = &ipkt->hdr;
  const uint8_t *data = ipkt->payload;

  // Pointer to the frame control section within the packet header
  const wifi_header_frame_control_t *frame_ctrl = (wifi_header_frame_control_t *)&hdr->frame_ctrl;

  // Parse MAC addresses contained in packet header into human-readable strings
  char addr1[] = "00:00:00:00:00:00\0";
  char addr2[] = "00:00:00:00:00:00\0";
  char addr3[] = "00:00:00:00:00:00\0";

  mac2str(hdr->addr1, addr1);
  mac2str(hdr->addr2, addr2);
  mac2str(hdr->addr3, addr3);


  // Print ESSID if beacon
  if (frame_ctrl->type == WIFI_PKT_MGMT && frame_ctrl->subtype == BEACON)
  {
    const wifi_mgmt_beacon_t *beacon_frame = (wifi_mgmt_beacon_t*) ipkt->payload;
    char ssid[32] = {0};

    if (beacon_frame->tag_length >= 32)
    {
      strncpy(ssid, beacon_frame->ssid, 31);
    }
    else
    {
      strncpy(ssid, beacon_frame->ssid, beacon_frame->tag_length);
    }

    // Output info to serial
    Serial.printf("\n %s | %s | %u | %02d         | ",
                  addr1,
                  addr2,
                  wifi_get_channel(),
                  ppkt->rx_ctrl.rssi
                 );

    Serial.printf("%s", ssid);
  }
}


/******************************************************************************/
void setup()
{
  // Serial setup
  Serial.begin(115200);
  delay(10);
  wifi_set_channel(9);

  // Wifi setup
  wifi_set_opmode(STATION_MODE);
  wifi_promiscuous_enable(0);
  WiFi.disconnect();

  // Set sniffer callback
  wifi_set_promiscuous_rx_cb(wifi_sniffer_packet_handler);
  wifi_promiscuous_enable(1);

  // Print header
  Serial.printf("\n\n     MAC Address 1|      MAC Address 2|      MAC Address 3| Ch| RSSI| Pr| T(S)  |           Frame type         |TDS|FDS| MF|RTR|PWR| MD|ENC|STR|   SSID");

}





/******************************************************************************/
int channel_iterator = 1;
int i = 0;

void loop()
{
  for ( i = 0 ; i < 5000 ; i++) {
  }

  if (channel_iterator == 16) {
    channel_iterator = 1;
  } else {
    channel_iterator++;
  }
  wifi_set_channel(channel_iterator);


  delay(10);
}

/*
  // According to the SDK documentation, the packet type can be inferred from the
  // size of the buffer. We are ignoring this information and parsing the type-subtype
  // from the packet header itself. Still, this is here for reference.
  wifi_promiscuous_pkt_type_t packet_type_parser(uint16_t len)
  {
    switch(len)
    {
      // If only rx_ctrl is returned, this is an unsupported packet
      case sizeof(wifi_pkt_rx_ctrl_t):
      return WIFI_PKT_MISC;

      // Management packet
      case sizeof(wifi_pkt_mgmt_t):
      return WIFI_PKT_MGMT;

      // Data packet
      default:
      return WIFI_PKT_DATA;
    }
  }
*/
