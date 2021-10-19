let loginBtn = document.querySelector('#login-button');

if(loginBtn) {
    //Canda notification checkbox show
    loginBtn.addEventListener('click', (event) => {
        event.preventDefault();

        const formData = new FormData();
        const email = document.querySelector('#email').value;
        const password = document.querySelector('#password').value;

        if(!email || !password) {
            alert('Email and password should not be empty');
            return false;
        }

        if(!validateEmail(email)) {
            alert('Invalid Email');
            return false;
        }

        formData.append('email', email);
        formData.append('password', password);

        fetch('/application/login', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                window.localStorage.setItem('access_token', data.access_token);
                window.location.href = data.redirect_url;
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    });
}

let logoutBtn = document.querySelector('#logout');
if(logoutBtn) {
    logoutBtn.addEventListener('click', (event) => {
        event.preventDefault();
        fetch('/application/logout', {
            method: 'POST',
            headers: new Headers({
                'Authorization': 'Bearer '+ window.localStorage.getItem('access_token'),
                'Content-Type': 'application/x-www-form-urlencoded'
            }),
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                window.localStorage.removeItem('access_token');
                window.location.href = data.redirect_url;
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    });
}

let saveVehicleBtn = document.querySelector('#save-vehicle');
if(saveVehicleBtn) {
    saveVehicleBtn.addEventListener('click', (event) => {
        event.preventDefault();

        let form = document.querySelector('#add-vehicle-form');
        const formData = new FormData(form);
        const sendFormData = new URLSearchParams();

        for(var pair of formData.entries()) {
            if(pair[1] === '') {
                alert(pair[0] + ' should not be empty')
                return false;
            }
            console.log(pair[0] + ' - ' + pair[1]);
            sendFormData.append(pair[0], pair[1])
        }

        fetch('/api/vehicles/add', {
            method: 'POST',
            body: sendFormData.toString(),
            headers: new Headers({
                'Authorization': 'Bearer '+ window.localStorage.getItem('access_token'),
                'Content-Type': 'application/x-www-form-urlencoded'
            }),
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if(data.status === 'success') {
                window.location.href = data.redirect_url;
            } else {
                if(data.errors) {
                    let errorMsg = '';
                    data.errors.forEach((element) => {
                        errorMsg += element + "\n";
                    });
                    alert(errorMsg);
                } else {
                    alert(data.message);
                }
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    });
}

let vehicleTypeSelect = document.querySelector('#vehicle-type');
if(vehicleTypeSelect) {
    vehicleTypeSelect.addEventListener('input', (event) => {
        event.preventDefault();

        let vehicleType = vehicleTypeSelect.value;
        if(vehicleType === '') {
            return false;
        }

        let getUrl = '';
        if(window.location.pathname === '/application/addVehicle') {
            getUrl = '/api/vehicles/types';
        } else if (window.location.pathname === '/application/showVehicles') {
            getUrl = '/api/vehicles';
        }

        fetch(getUrl + '?vehicle_type=' + vehicleType, {
            method: 'GET',
            headers: new Headers({
                'Authorization': 'Bearer '+ window.localStorage.getItem('access_token'),
                'Content-Type': 'application/x-www-form-urlencoded'
            }),
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                if(window.location.pathname === '/application/addVehicle') {
                    renderSedanTypes(data)
                } else if (window.location.pathname === '/application/showVehicles') {
                    renderVehiclesTable(data, vehicleType);
                }
            } else {
                // Error
                window.location.href = data.redirect_url;
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    });
}

function renderVehiclesTable(data, vehicleType) {
    let vehiclesTable = document.querySelector('#vehicles-table');
    vehiclesTable.innerHTML = '';

    let headersHtml = '<thead>';
    headersHtml += '<tr>';

    data.table_headers.forEach((element) => {
        console.log(element)
        headersHtml += '<th scope="col">' + element.replace('_',' ') +'</th>';
    });

    headersHtml += '</tr>';
    headersHtml += '</thead>';

    let tableBodyHtml = '<tbody>';

    data.data.forEach((element) => {
        console.log(element)
        tableBodyHtml += '<tr>';

        if(vehicleType === 'sedan') {
            tableBodyHtml += '<th scope="row">' + element.sedan_id + '</th>';
            tableBodyHtml += '<td>' + element.vehicle_id + '</td>';
            tableBodyHtml += '<td>' + element.sedan_type + '</td>';
        } else if(vehicleType === 'motorcycle') {
            tableBodyHtml += '<th scope="row">' + element.motorcycle_id + '</th>';
            tableBodyHtml += '<td>' + element.vehicle_id + '</td>';
            tableBodyHtml += '<td>' + element.motorcycle_type + '</td>';
        }
        tableBodyHtml += '<td>' + element.horsepower + '</td>';
        tableBodyHtml += '<td>' + element.total_tires + '</td>';
        tableBodyHtml += '<td>' + element.model + '</td>';
        tableBodyHtml += '<td>' + element.color + '</td>';
        tableBodyHtml += '</tr>';
    });

    tableBodyHtml += '<tbody>';

    vehiclesTable.innerHTML = headersHtml + tableBodyHtml;
}

function renderSedanTypes(data){
    let subTypeSelect = document.querySelector('#sub-type');
    subTypeSelect.innerHTML = '';

    let selectHtml = '<option value="">Select Type</option>';

    data.data.forEach((element) => {
        selectHtml +='<option value="' + element + '">' + element + '</option>'
    });

    subTypeSelect.innerHTML = selectHtml;
    subTypeSelect.parentNode.style.display = 'block'
}

function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}