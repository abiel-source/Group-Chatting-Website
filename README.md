# GroupChat
## Demo:
https://user-images.githubusercontent.com/31304414/124683690-7251a480-de82-11eb-9b25-f50a9ea19a37.mp4

This demo shows a quick conversation between me (laptop) and my imaginary friend (mobile). Although this chat can support multiple individuals at once, who's to say that a group can't have just 2 people?

# Author Notes

you can visit the website under the domain name: "groupchat.cloud"

however, the chat-server.php file must be executed on the server-side in order for the chat to run.
This SSH access information will be kept private.

INTERSERVER PORTS:
when running chat-server.php, use dynamic ports in the range: 49152–65535
As other standard http ports are taken 

# Login & Registration

Implementation of login and registration protocols via the PHP mysqli interface and SQL DML commands. Both protocols are self-handling; they send and receive form data via POST HTTP requests, to which simple validating algorithms - linear time complexity - are dispatched. If validated and successful, PHP session variables are initialized and the client is redirected to the main page.

# Session Variables

In order to allow the client to engage with multiple accounts simultaneously on the same browser, such that these accounts have a one-to-one mapping to the set of tabs: a 2-level session data storage protocol was constructed using PHP's superglobal session object and JS's session storage interface. Upon login success, PHP initializes user data (scope includes all tabs) and passes it to the main document's session storage (scope excludes other tabs) where it will then comfortably interact with the server. The PHP session variables then become free memory to be overwritten for the next tab.

# Active Users Aside

An active users feature was implemented by keeping track of all one-to-many mappings between clients and their connections/tabs. This was implemented as a 2D associative array of arrays, where each key is an active username and each subarray are all active connections for that user. Upon successful connection, a 4-step request process is initiated by the client in order for the server to deduce the username given their connection ID.

1) Client => socket connection => Server
2) Server => request for user information => Client
3) Client => response with user information => Server
4) Server => updated key-list of active users => Client


# Main Chat

The socket programming was mostly driven by the PHP Ratchet API, allowing me to reduce development time and avoid reinventing the wheel. JSON data sent between the server and client are parsed, validated and processed by my methods and event handlers. Jquery was also used for more convenient DOM manipulation. Soon, functionality to store chat records on the server will be supported as well as to persist chat data upon refresh.
