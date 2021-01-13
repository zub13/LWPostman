# LWPostman

This small and simple application allows you to send http requests and returns the response.

The whole application is coded in PHP 7, JS and Html & Css.

The UI is composed of the following elements:

- App header
- Error Panel, only visible if there is an error
- Inputs, composed of a selectbox that allows to select the HTTP Request method, an input for the Http URL and a button to submit the request
- Tabs that allow you to input query parameters that will be added to the URL, Http Header and Body where you can write the JSON data. The Body section also provides a button to validate the JSON string. If the validation is correct the textarea background will be changed to green else to red. 
- Reponse area where it displays the response as well as the response header. The header and response header background color will change according to the Http response. If the status code is not in the range of 200 or 300, it will be displayed as red, otherwise in green.

You will need to point the PHP server document root to the public directory of the project.
No external library has been used except the https://kit.fontawesome.com/a076d05399.js for the buttons.
