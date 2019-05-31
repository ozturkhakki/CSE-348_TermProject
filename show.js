var once_populate_districts = false;
var once_populate_markets = false;

function installDB() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        console.log(this.responseText);
    };
    xmlhttp.open("GET", "install.php?action=install", true);
    xmlhttp.send();
}

function deleteDB() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        console.log(this.responseText);
    };
    xmlhttp.open("GET", "install.php?action=delete", true);
    xmlhttp.send();
}

function populate_cities() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        var div = document.getElementById("div_cities");
        while (div.firstChild) {
            div.removeChild(div.firstChild);
        }

        var select = document.createElement("select");
        select.id = "select_cities";
        div.appendChild(select);

        var option = document.createElement("option");
        option.innerHTML = "Select City";
        option.selected = true;
        option.disabled = true;
        select.appendChild(option);

        var splitResponse = this.responseText.split(',');
        for (var i = 0; i < splitResponse.length; i++) {
            option = document.createElement("option");
            option.innerHTML = splitResponse[i];
            select.appendChild(option);
        }
    };
    var el = document.getElementById("select_districts");
    var selected = encodeURI(el.options[el.selectedIndex].text);
    xmlhttp.open("GET", "show.php?select=cities&district=" + selected, true);
    xmlhttp.send();
}

function populate_districts() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (once_populate_districts == true) return;
        if (!this.responseText || this.responseText.length === 0) return;

        var select = document.createElement("select");
        select.id = "select_districts";
        select.onchange = populate_cities;
        document.getElementById("div_districts").appendChild(select);

        var option = document.createElement("option");
        option.innerHTML = "Select District";
        option.selected = true;
        option.disabled = true;
        select.appendChild(option);

        var splitResponse = this.responseText.split(',');
        for (var i = 0; i < splitResponse.length; i++) {
            option = document.createElement("option");
            option.value = splitResponse[i];
            option.innerHTML = splitResponse[i];
            select.appendChild(option);
        }

        select = document.createElement("select");
        select.id = "select_cities";
        select.onchange = show_markets_sale_count;
        document.getElementById("div_cities").appendChild(select);

        option = document.createElement("option");
        option.innerHTML = "Select City";
        option.selected = true;
        option.disabled = true;
        select.appendChild(option);

        once_populate_districts = true;
    };
    xmlhttp.open("GET", "show.php?select=districts", true);
    xmlhttp.send();
}

function populate_markets() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (once_populate_markets == true) return;
        if (!this.responseText || this.responseText.length === 0) return;

        var select = document.createElement("select");
        select.id = "select_markets";
        select.onchange = function() {
            populate_salesmans();
            populate_customers();
        };
        document.getElementById("div_markets").appendChild(select);

        var option = document.createElement("option");
        option.innerHTML = "Select Market";
        option.selected = true;
        option.disabled = true;
        select.appendChild(option);

        var splitResponse = this.responseText.split(',');
        for (var i = 0; i < splitResponse.length; i++) {
            option = document.createElement("option");
            option.value = splitResponse[i];
            option.innerHTML = splitResponse[i];
            select.appendChild(option);
        }

        // Add empty select for salesmans
        var select_salesmans = document.createElement("select");
        select_salesmans.id = "select_salesmans";
        document.getElementById("div_salesmans").appendChild(select_salesmans);

        var option_salesmans = document.createElement("option");
        option_salesmans.innerHTML = "Select Salesman";
        option_salesmans.selected = true;
        option_salesmans.disabled = true;
        select_salesmans.appendChild(option_salesmans);

        // Add empty select for customers
        var select_customers = document.createElement("select");
        select_customers.id = "select_customers";
        document.getElementById("div_customers").appendChild(select_customers);

        var option_customers = document.createElement("option");
        option_customers.innerHTML = "Select Customer";
        option_customers.selected = true;
        option_customers.disabled = true;
        select_customers.appendChild(option_customers);

        once_populate_markets = true;
    };
    xmlhttp.open("GET", "show.php?select=markets", true);
    xmlhttp.send();
}

window.onload = function () {
    populate_districts();
    populate_markets();
}

function populate_salesmans() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        var div = document.getElementById("div_salesmans");
        while (div.firstChild) {
            div.removeChild(div.firstChild);
        }

        var select = document.createElement("select");
        select.id = "select_salesmans";
        div.appendChild(select);

        var option = document.createElement("option");
        option.innerHTML = "Select Salesman";
        option.selected = true;
        option.disabled = true;
        select.appendChild(option);

        var splitResponse = this.responseText.split(',');
        for (var i = 0; i < splitResponse.length; i++) {
            option = document.createElement("option");
            option.innerHTML = splitResponse[i];
            select.appendChild(option);
        }
    };
    var el = document.getElementById("select_markets");
    var selected = encodeURI(el.options[el.selectedIndex].text);
    xmlhttp.open("GET", "show.php?select=salesmans&market=" + selected, true);
    xmlhttp.send();
}

function populate_customers() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        var div = document.getElementById("div_customers");
        while (div.firstChild) {
            div.removeChild(div.firstChild);
        }

        var select = document.createElement("select");
        select.id = "select_customers";
        div.appendChild(select);

        var option = document.createElement("option");
        option.innerHTML = "Select Customer";
        option.selected = true;
        option.disabled = true;
        select.appendChild(option);

        var splitResponse = this.responseText.split(',');
        for (var i = 0; i < splitResponse.length; i++) {
            option = document.createElement("option");
            option.innerHTML = splitResponse[i];
            select.appendChild(option);
        }
    };
    var el = document.getElementById("select_markets");
    var selected = encodeURI(el.options[el.selectedIndex].text);
    xmlhttp.open("GET", "show.php?select=customers&market=" + selected, true);
    xmlhttp.send();
}

function show_markets_sale_count() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        var div = document.getElementById("div_result");
        while (div.firstChild) {
            div.removeChild(div.firstChild);
        }

        var splitResponse = this.responseText.split(';');
        var arr_markets = splitResponse[0].split(',');
        var arr_datas = splitResponse[1].split(',');

        var ctx = document.createElement("canvas");
        var myChart = new Chart(ctx, {
            type: 'polarArea',
            data: {
                labels: [arr_markets[0], arr_markets[1], arr_markets[2], arr_markets[3], arr_markets[4]],
                datasets: [{
                    label: '# of Sales of Markets',
                    data: [arr_datas[0], arr_datas[1], arr_datas[2], arr_datas[3], arr_datas[4]],
                    backgroundColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ]
                }]
            }
        });

        div.appendChild(ctx);

    };
    var el = document.getElementById("select_cities");
    var selected = encodeURI(el.options[el.selectedIndex].text);
    xmlhttp.open("GET", "show.php?action=show_markets_sale_count&city=" + selected, true);
    xmlhttp.send();
}

function show_market_product_sales() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        var div = document.getElementById("div_result");
        while (div.firstChild) {
            div.removeChild(div.firstChild);
        }

        var splitResponse = this.responseText.split(';');
        var arr_products = splitResponse[0].split(',');
        var arr_sell_counts = splitResponse[1].split(',');

        var ctx = document.createElement("canvas");
        ctx.getContext("2d").canvas.height = 5 * arr_products.length;
        var myChart = new Chart(ctx, {
            responsive: true,
            type: 'horizontalBar',
            data: {
                labels: arr_products,
                datasets: [{
                    label: 'Products Sell Count',
                    data: arr_sell_counts,
                    backgroundColor: 'rgba(255, 99, 132, 1)'
                }]
            }
        });

        div.appendChild(ctx);

    };
    var el = document.getElementById("select_markets");
    var selected = encodeURI(el.options[el.selectedIndex].text);
    xmlhttp.open("GET", "show.php?action=show_market_product_sales&market=" + selected, true);
    xmlhttp.send();
}

function show_market_salesman_sales() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        var div = document.getElementById("div_result");
        while (div.firstChild) {
            div.removeChild(div.firstChild);
        }

        var splitResponse = this.responseText.split(';');
        var arr_salesmans = splitResponse[0].split(',');
        var arr_sell_counts = splitResponse[1].split(',');

        var ctx = document.createElement("canvas");
        ctx.getContext("2d").canvas.height = 5 * arr_salesmans.length;
        var myChart = new Chart(ctx, {
            responsive: true,
            type: 'horizontalBar',
            data: {
                labels: arr_salesmans,
                datasets: [{
                    label: 'Salesmans Sell Count',
                    data: arr_sell_counts,
                    backgroundColor: 'rgba(255, 99, 132, 1)'
                }]
            }
        });

        div.appendChild(ctx);
    };
    var el = document.getElementById("select_markets");
    var selected = encodeURI(el.options[el.selectedIndex].text);
    xmlhttp.open("GET", "show.php?action=show_market_salesman_sales&market=" + selected, true);
    xmlhttp.send();
}

function show_salesman_product_sales() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        var div = document.getElementById("div_result");
        while (div.firstChild) {
            div.removeChild(div.firstChild);
        }

        var splitResponse = this.responseText.split(';');
        var arr_salesmans = splitResponse[0].split(',');
        var arr_sell_counts = splitResponse[1].split(',');

        var ctx = document.createElement("canvas");
        var myChart = new Chart(ctx, {
            responsive: true,
            type: 'polarArea',
            data: {
                labels: arr_salesmans,
                datasets: [{
                    label: 'Salesmans Sell Count',
                    data: arr_sell_counts,
                    backgroundColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ]
                }]
            }
        });

        div.appendChild(ctx);
    };
    var el = document.getElementById("select_salesmans");
    var selected = encodeURI(el.options[el.selectedIndex].text);
    xmlhttp.open("GET", "show.php?action=show_salesman_product_sales&salesman=" + selected, true);
    xmlhttp.send();
}

function show_customer_purchases() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        console.log(this.responseText);

        var div = document.getElementById("div_result");
        while (div.firstChild) {
            div.removeChild(div.firstChild);
        }

        var splitResponse = this.responseText.split(';');
        var arr_product_names = splitResponse[0].split(',');
        var arr_product_prices = splitResponse[1].split(',');
        var arr_sale_dates = splitResponse[2].split(',');

        // Add empty select for customers
        var table = document.createElement("table");
        table.id = "table_customer_purchases";
        table.style = "width:900px;";
        div.appendChild(table);

        var tr = document.createElement("tr");
        table.appendChild(tr);

        var td = document.createElement("td");
        td.style = "background-color: #F4C724;";
        td.innerHTML = "PRODUCT NAME";
        tr.appendChild(td);

        td = document.createElement("td");
        td.style = "background-color: #F4C724;";
        td.innerHTML = "PRODUCT PRICE";
        tr.appendChild(td);

        td = document.createElement("td");
        td.style = "background-color: #F4C724;";
        td.innerHTML = "SALE DATE";
        tr.appendChild(td);

        var total_price = 0;
        for (var i = 0; i < arr_product_names.length; i++) {
            var tr = document.createElement("tr");
            table.appendChild(tr);

            var td = document.createElement("td");
            td.innerHTML = arr_product_names[i];
            tr.appendChild(td);

            td = document.createElement("td");
            td.innerHTML = arr_product_prices[i];
            total_price = +total_price + +arr_product_prices[i];
            tr.appendChild(td);

            td = document.createElement("td");
            td.innerHTML = arr_sale_dates[i];
            tr.appendChild(td);
        }

        var tr = document.createElement("tr");
        table.appendChild(tr);

        var td = document.createElement("td");
        tr.appendChild(td);

        td = document.createElement("td");
        td.style = "background-color: #e47833;";
        td.innerHTML = total_price;
        tr.appendChild(td);

        td = document.createElement("td");
        tr.appendChild(td);

        once_populate_markets = true;
    };
    var el = document.getElementById("select_markets");
    var selected_market = encodeURI(el.options[el.selectedIndex].text);
    el = document.getElementById("select_customers");
    var selected_customer = encodeURI(el.options[el.selectedIndex].text);
    xmlhttp.open("GET", "show.php?action=show_customer_purchases&market=" + selected_market + 
        "&customer=" + selected_customer, true);
    xmlhttp.send();
}

function show_allsales_eachdistrict() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        var div = document.getElementById("div_result");
        while (div.firstChild) {
            div.removeChild(div.firstChild);
        }

        var splitResponse = this.responseText.split(';');
        var arr_districts = splitResponse[0].split(',');
        var arr_sell_counts = splitResponse[1].split(',');

        var ctx = document.createElement("canvas");
        var myChart = new Chart(ctx, {
            responsive: true,
            type: 'polarArea',
            data: {
                labels: arr_districts,
                datasets: [{
                    label: 'Districts Sell Count',
                    data: arr_sell_counts,
                    backgroundColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 206, 86, 1)'
                    ]
                }]
            }
        });

        div.appendChild(ctx);
    };
    xmlhttp.open("GET", "show.php?action=show_allsales_eachdistrict", true);
    xmlhttp.send();
}

function show_allsales_eachmarket() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        console.log(this.responseText);

        var div = document.getElementById("div_result");
        while (div.firstChild) {
            div.removeChild(div.firstChild);
        }

        var splitResponse = this.responseText.split(';');
        var arr_markets = splitResponse[0].split(',');
        var arr_sell_counts = splitResponse[1].split(',');

        var ctx = document.createElement("canvas");
        var myChart = new Chart(ctx, {
            responsive: true,
            type: 'polarArea',
            data: {
                labels: arr_markets,
                datasets: [{
                    label: 'Markets Sell Count',
                    data: arr_sell_counts,
                    backgroundColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ]
                }]
            }
        });

        div.appendChild(ctx);
    };
    xmlhttp.open("GET", "show.php?action=show_allsales_eachmarket", true);
    xmlhttp.send();
}