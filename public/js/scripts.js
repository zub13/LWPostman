/*
    Maintains the state of the request type
*/
const setSelectedValue = function () {
    const cookies = document.cookie.split(';');
    cookies.forEach(cookie => {
        if (cookie.includes('SELECTED_REQUEST_TYPE')) {
            const requestType = cookie.split('=')[1];
            const element = document.getElementById("httpRequest");
            element.value = requestType;
        }
    });

    // reset the style attached dynamically to the textarea for displaying JSON content
    document.getElementById("jsonBody").removeAttribute("style");
};

setSelectedValue();
openDefaultNav();

/*
    Sets a cookie to maintain the value of the selected option in the select field.
 */
function selectOnChange(selectedRequest) {
    document.cookie = escape("SELECTED_REQUEST_TYPE") + "=" + escape(selectedRequest.value);
    document.getElementById('responseCode').innerText = '';
    document.getElementById('responseHeader').innerText = '';
    location.reload();
}

/*
    Sets the value of the hidden fields by the number of the rows in the query params and headers tables
 */
function fillHiddenFields() {
    const headersTable = document.getElementById("headersTable").getElementsByTagName('tbody')[0];
    document.getElementById('hiddenFieldHeadersRowCount').value = headersTable.rows.length;

    const queryParamTable = document.getElementById("paramsTable").getElementsByTagName('tbody')[0];
    document.getElementById('hiddenFieldQueryParamsRowCount').value = queryParamTable.rows.length;

    console.log("Headers row count and Query params row counts are set.");
    return true;
}

/*
    Concatenates the query params to the URL string
 */
function concat2url(x) {
    if (!x.id.includes("k")) {
        const k = x.id.replace("v", "k");
        if (document.getElementById(k).value !== null) {
            const urlVal = document.getElementById("url").value;
            if (urlVal !== null && urlVal !== "") {
                if (urlVal.includes('?')) {
                    document.getElementById("url").value = document.getElementById("url").value + "&" + document.getElementById(k).value + "=" + document.getElementById(x.id).value;
                } else {
                    document.getElementById("url").value = document.getElementById("url").value + "?" + document.getElementById(k).value + "=" + document.getElementById(x.id).value;
                }
                console.log("Params concatenated to the URL: ".document.getElementById("url").value);
            }
        }
    }
}

/*
    Initialisation call to open the default tabs.
 */
function openDefaultNav() {
    let i, tabcontent, tabcontentResponse, tablinks, tablinksResponse;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    tabcontentResponse = document.getElementsByClassName("tabcontentResponse");
    for (i = 0; i < tabcontentResponse.length; i++) {
        tabcontentResponse[i].style.display = "none";
    }
    tabcontentResponse[0].className += " active";

    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    tablinks[0].className += " active";

    tablinksResponse = document.getElementsByClassName("tablinksResponse");
    for (i = 0; i < tablinksResponse.length; i++) {
        tablinksResponse[i].className = tablinksResponse[i].className.replace(" active", "");
    }
    tablinksResponse[0].className += " active";

    document.getElementById("params").style.display = "block";
    document.getElementById("response").style.display = "block";

    console.log("Default tabs are active.");
}

/*
    Opens a tab in the request part
 */
function openNav(evt, tabName) {
    let i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

/*
    Opens a tab in the response part
 */
function openNavResponse(evt, tabName) {
    let i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontentResponse");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinksResponse");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

/*
    Adds dynamically a new row in the table
 */
function addNewRowRequest(x) {
    const table = document.getElementById(x);
    let rowCount = table.rows.length;
    const row = table.insertRow(rowCount);
    row.id = x + "row" + rowCount;

    // increment row index
    rowCount = rowCount - 1;

    let templateKeyId, templateValueId;
    if (x === "paramsTable") {
        templateKeyId = "ptk" + rowCount;
        templateValueId = "ptv" + rowCount;
    } else {
        templateKeyId = "htk" + rowCount;
        templateValueId = "htv" + rowCount;
    }
    // add 3 new cell and set the html content
    const cell1 = row.insertCell(0);
    const cell2 = row.insertCell(1);
    const cell3 = row.insertCell(2);

    cell1.innerHTML = "<input type=\"text\" id=\"" + templateKeyId + "\" name=\"" + templateKeyId + "\"  onchange='concat2url(this)'>";
    cell2.innerHTML = "<input type=\"text\" id=\"" + templateValueId + "\" name=\"" + templateValueId + "\" onchange='concat2url(this)'>";
    cell3.innerHTML = "<button type=\"button\" onclick='removeRowRequest(\"" + row.id + "\")'>&#10006;</button>";
    console.log("New row added to the table with ID " + x);
}

/*
    Removes an existing row from the table
 */
function removeRowRequest(x) {
    const row = document.getElementById(x);
    const url = document.getElementById("url").value;
    if (url !== null && url.includes(row.getElementsByTagName('input')[0].value) + "=") {
        document.getElementById("url").value = document.getElementById("url").value.replace(row.getElementsByTagName('input')[0].value + "=" + row.getElementsByTagName('input')[1].value, "");
        if (document.getElementById("url").value.slice(-1) === '?') {
            document.getElementById("url").value = document.getElementById("url").value.replace("?", "");
        }
    }
    row.parentNode.removeChild(row);
    console.log("Row removed from the table with ID " + x);
}

/*
    Parses the json content and returns false if an exception is thrown else returns true.
    It also sets the background color of the textarea according to the json validation result.
 */
function isValidJSON() {
    const data = document.getElementById("jsonBody").value;
    if (data !== null) {
        try {
            JSON.parse(data);
            document.getElementById("jsonBody").setAttribute("style", "background-color:#7dce7d");
            console.log("JSON data is valid");
        } catch (e) {
            document.getElementById("jsonBody").setAttribute("style", "background-color:#ea7ea3");
            console.log("Invalid JSON data: " + e.message);
            return false;
        }
    }
    return true;
}

function focusErrorUrl(){
    document.getElementById("url").setAttribute("style", "outline-color:#ea7ea3");
}

function resetErrorUrl(){
    document.getElementById("url").removeAttribute("style");
}