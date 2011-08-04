
// ошибки полученные с сервера
function SimpleServiceError(message, code) {
    //alert(message);
    Error.call(this, message);
    this.message = message;
    this.code = code;
}

SimpleServiceError.prototype = new Error;

SimpleServiceError.constructor = SimpleServiceError;


//Клиент SimpleService
SimpleService = function (address) {
    this.address = address;
}

SimpleService.prototype.exec = function() {

    //Обработка параметров функции
    var action = "";
    var args = {};
    var success = function(a) {};
    var error = function(a) {};
    
    if (arguments.length == 0) {
        //выполняется действие по умолчанию без параметров
    }
    else if (arguments.length == 1) {
        action = arguments[0];
    }
    else if (arguments.length == 2) {
        action = arguments[0];
        args = arguments[1];
    }
    else if (arguments.length == 3) {
        action = arguments[0];
        args = arguments[1];
        if (arguments[2] != null)
            success = arguments[2];
    }
    else if (arguments.length == 4) {
        action = arguments[0];
        args = arguments[1];
        if (arguments[2] != null)
            success = arguments[2];
        if (arguments[3] != null)
            error = arguments[3];
    }
    else {
        throw new Error("Invalid number of arguments");
    }

    if (action == "") {
        actionStr = "";
    }
    else {
        actionStr = "?action=" + action;
    }
    //вызыв сервиcа на сервере, синхронно
    var result = null;
        $.ajax({
        url: this.address + actionStr,
        data: args,
        success: function (json) {
            if (typeof (json.status) == "undefined") {
                result = new Error("Result status is undefined");
                return;
            }
            switch (json.status) {
                case "success":
                    result = json.value;
                    break;
                case "error":
                    result = new SimpleServiceError(json.value.message, json.value.code);
                    break;
                default:
                    result = new Error("Invalid result status " + json.status);
                    break;
            }
            if (result instanceof Error) {
                error(result);
            }
            else {
                success(result);
            }
        },
        error: function(jqXHR, status, errorThrown) {
            if (typeof(errorThrown) == "undefined" || errorThrown == null)
                error(new Error(status));
            else
                error(errorThrown);
        },
            /*
        complete: function(jqXHR, status) {
            if (textStatus != 'success' && textStatus != 'error') {
                ;
            }
        },
        */
        dataType: "json",
        async: true
    });

}

SimpleService.prototype.execSync = function () {
    //Обработка параметров функции
    var action = "";
    var args = {};
    if (arguments.length == 0) {
        //выполняется действие по умолчанию без параметров
    }
    else if (arguments.length == 1) {
        action = arguments[0];
    }
    else if (arguments.length == 2) {
        action = arguments[0];
        args = arguments[1];
    }
    else {
        throw new Error("Invalid number of arguments");
    }

    if (action == "") {
        actionStr = "";
    }
    else {
        actionStr = "?action=" + action;
    }
    //вызыв сервиcа на сервере, синхронно
    var result = null;
        $.ajax({
        url: this.address + actionStr,
        data: args,
        success: function (json) {
            if (typeof (json.status) == "undefined") {
                result = new Error("Result status is undefined");
                return;
            }
            switch (json.status) {
                case "success":
                    result = json.value;
                    break;
                case "error":
                    result = new SimpleServiceError(json.value.message, json.value.code);
                    break;
                default:
                    result = new Error("Invalid result status " + json.status);
                    break;
            }
        },
        dataType: "json",
        async: false
    });

    if (result instanceof Error) {
     throw result;
    }

    return result;
}

SimpleService.prototype.getActions = function () {
    return this.execSync("get_action_list");
}

SimpleService.prototype.getActionInfo = function (action) {
    return this.execSync("get_action_info", { action_name: action });
}



