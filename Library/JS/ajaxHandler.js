/*
	AjaxHandler(baseUrl)
	Closure providing standardized AJAX request methods with error handling support
		baseUrl - string - base URL prepended to all request endpoints

	returns object with GET, POST, PUT, DELETE, and setErrorCB methods
*/
function AjaxHandler(baseUrl = "") {
    let errorCB;
	/*
		request(endpoint, method, data)
		Private core function that performs the actual AJAX request
			endpoint - string - API endpoint path
			method - string - HTTP method (GET, POST, PUT, DELETE)
			data - object (optional) - request payload

		returns jQuery promise
	*/
    function request(endpoint, method, data = null) {
        return $.ajax({
            url: baseUrl + endpoint,
            method: method,
            data: data,
            dataType: "json",
            contentType: "application/json; charset=utf-8",
            processData: method === "GET",
            error: (xhr, status, err) => {
                console.log("AJAX Error:", status, err);
				if(errorCB)errorCB(xhr,status,err);
            }
        });
    }

    // Public API exposed by the closure
    return {
		/*
			setErrorCB(cb)
			Sets a callback function to be called on AJAX errors
				cb - function - callback receiving (xhr, status, err) parameters
		*/
		setErrorCB (cb)
		{
			errorCB=cb;
		},
        GET(endpoint, params = {}) 
		{
            return request(endpoint, "GET", params);
        },
        POST(endpoint, payload = {}) 
		{
            return request(endpoint, "POST", JSON.stringify(payload));
        },
        PUT(endpoint, payload = {}) 
		{
            return request(endpoint, "PUT", JSON.stringify(payload));
        },
        DELETE(endpoint, payload = {}) 
		{
            return request(endpoint, "DELETE", JSON.stringify(payload));
        }
    };
}

window.ajaxHandler = AjaxHandler;