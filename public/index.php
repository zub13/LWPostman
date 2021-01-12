<?php
include('services/request.php');

//phpinfo();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <title>Light-weight Postman</title>
</head>

<body>
<?php
$request = $url = $content = $response = $responseHeader = $errorMsg = $errorCode = "";
$headers = $queryParams = array();

$isReload = $isError = false;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $headersRowCount = $_POST["hiddenFieldHeadersRowCount"];
    $intVal = (int)$headersRowCount;
    for ($i = 0; $i <= $intVal - 1; $i++) {
        $strVal = (string)$i;
        $key = $_POST["htk" . $strVal];
        $value = $_POST["htv" . $strVal];
        if($key !== '' && $value !== ''){
            $headers[$i] = array();
            $headers[$i]["htk" . $strVal] = cleanInput($_POST["htk" . $strVal]);
            $headers[$i]["htv" . $strVal] = cleanInput($_POST["htv" . $strVal]);
        }
    }

    $request = cleanInput($_POST["request"]);
    $url = cleanInput($_POST["url"]);

    if ($url !== null) {
        $url = str_replace(" ", "%", $url);
    }

    $jsonContent = $_POST["jsonBody"];
    $content = json_decode($jsonContent, true);

    $httpRequest = new request($url, $request, $headers, $content);
    $httpExecution = $httpRequest->doRequest();
    if (isset($httpExecution['error']) && $httpExecution['error'] === true) {
        $isError = true;
        $errorMsg = $httpExecution['errorMsg'];
        $errorCode = $httpExecution['errorCode'];
    } else {
        $isError = false;
    }

    if (is_array($httpExecution['response'])) {
        $response = json_encode($httpExecution['response'], JSON_PRETTY_PRINT);
    } else {
        $response = $httpExecution['response'];
    }

    $responseHeader = json_encode($httpExecution['responseHeader'], JSON_PRETTY_PRINT);
    $responseCode = $httpExecution['responseCode'];
}

function cleanInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$requestTypes = array("GET", "POST", "PUT", "DELETE", "OPTIONS");
?>


<div class="container">
    <div class="title">LW Postman</div>
    <div class="errorpanel" <?php if ($isError) {
        echo "style = \"display:block\"";
    } else {
        echo "style = \"display:none\"";
    } ?>>
        <?php echo "Error: $errorMsg"; ?>
    </div>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
          onsubmit="return fillHiddenFields();">
        <div class="row">
            <div class="main-left">
                <select name="request" class="request-type" id="httpRequest" onchange="selectOnChange(this)">
                    <?php foreach ($requestTypes as $value) { ?>
                        <option value="<?php echo strtolower($value); ?>"
                            <?php
                            if (isset($_COOKIE['SELECTED_REQUEST_TYPE']) && $_COOKIE['SELECTED_REQUEST_TYPE'] === $value) {
                                echo "selected";
                            } ?>>
                            <?php echo $value; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="main-center">
                <input id="url" name="url" type="text" class="url-input" aria-label="URL" placeholder="URL"
                       value="<?php echo $url; ?>">
            </div>

            <div style="display: none">
                <input type="text" id="hiddenFieldHeadersRowCount" name="hiddenFieldHeadersRowCount">
                <input type="text" id="hiddenFieldQueryParamsRowCount" name="hiddenFieldQueryParamsRowCount">
            </div>

            <div class="main-right">
                <input type="submit" value="SEND" onclick="window.onbeforeunload = null;">
            </div>
        </div>

        <br>
        <div class="tab">
            <button type="button" class="tablinks" onclick="openNav(event, 'params')">Params</button>
            <button type="button" class="tablinks" onclick="openNav(event, 'header')">Headers</button>
            <button type="button" class="tablinks" onclick="openNav(event, 'body')">Body</button>
        </div>

        <div id="params" class="tabcontent">
            <h3>Query Params</h3>
            <table id="paramsTable">
                <thead>
                <tr>
                    <th>Key</th>
                    <th>Value</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr id="paramsTableRow0">
                    <td><input type="text" id="ptk0" name="ptk0" value="<?php  echo isset($_POST['ptk0']) ? $_POST['ptk0'] : ''?>" onchange='concat2url(this)'></td>
                    <td><input type="text" id="ptv0" name="ptv0" value="<?php  echo isset($_POST['ptv0']) ? $_POST['ptv0'] : ''?>" onchange='concat2url(this)'></td>
                    <td>
                        <button type="button" onclick='removeRowRequest("paramsTableRow0")'>&#10006; REMOVE</button>
                    </td>
                </tr>
                </tbody>
            </table>
            <br>
            <button type="button" onclick='addNewRowRequest("paramsTable")'>&#10009; ADD</button>
        </div>

        <div id="header" class="tabcontent">
            <h3>Headers</h3>
            <table id="headersTable">
                <thead>
                <tr>
                    <th>Key</th>
                    <th>Value</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr id="headersTableRow0">
                    <td><input type="text" id="htk0" name="htk0" value="<?php  echo isset($_POST['htk0']) ? $_POST['htk0'] : ''?>"></td>
                    <td><input type="text" id="htk0" name="htv0" value="<?php  echo isset($_POST['htv0']) ? $_POST['htv0'] : ''?>"></td>
                    <td>
                        <button type="button" onclick='removeRowRequest("headersTableRow0")'>&#10006; REMOVE</button>
                    </td>
                </tr>
                </tbody>
            </table>
            <br>
            <button type="button" onclick='addNewRowRequest("headersTable")'>&#10009; ADD</button>
        </div>

        <div id="body" class="tabcontent">
            <h3>JSON Body</h3>
            <textarea id="jsonBody" class="jsonBody" name="jsonBody" rows="10" cols="69"><?php  echo isset($_POST['jsonBody']) ? $_POST['jsonBody'] : ''?></textarea>
            <br>
            <button type="button" onclick="isValidJSON()">&#10003; VALIDATE</button>
        </div>
        <br>
    </form>


    <div class="tab">
        <button class="tablinksResponse" onclick="openNavResponse(event, 'response')">Body</button>
        <button class="tablinksResponse" onclick="openNavResponse(event, 'responseHeader')">Headers</button>
    </div>

    <?php
    $styleCode = "";
    if (isset($responseCode)) {
        if (strpos($responseCode, "2") === 0 || strpos($responseCode, "3") === 0) {
            $styleCode = "style=\"background-color:#7dce7d\"";
        } else {
            $styleCode = "style=\"background-color:#ea7ea3\"";
        }
    }
    ?>

    <div id="response" class="tabcontentResponse" <?php echo " " . $styleCode; ?>>
        <pre><code id="responseCode"><?php echo $response; ?></code></pre>
    </div>


    <div id="responseHeader" class="tabcontentResponse" <?php echo " " . $styleCode; ?>>
        <pre><code id="responseHeaderCode"><?php echo $responseHeader; ?></code></pre>
    </div>
</div>

<script src="js/scripts.js"></script>
</body>

</html>