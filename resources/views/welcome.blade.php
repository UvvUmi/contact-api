<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Kontaktai') }}</title>
    </head>
    <body style="display:flex; padding: 24px; align-items:center; flex-direction:column; font-family: Arial;">
        <h1 style="margin: 0px;">Kontaktų sąrašas</h1>
        <ul id="contacts-list" style="list-style-type: none;"></ul>
        <div style="display:none;" id="edit-form">
            <form id="form">
                <div style="margin-top: 10px;">
                    <label for="fname">Vardas</label><br>
                    <input type="text" id="fname" name="fname">
                </div>

                <div style="margin: 15px 0px 15px 0px;">
                    <label for="email">Paštas</label><br>
                    <input type="email" id="email" name="email" style="width:175px;">
                </div>
            </form>
            <button id="submitBtn">Redaguoti</button>
            <button onclick="toggleEdit()">Atšaukti</button>
        </div>
        <script>
            function indexContacts() {
                fetch('http://localhost:8000/api/contacts')
                .then(response => response.json())
                .then(contacts => {
                    const list = document.getElementById('contacts-list');
                    contacts.forEach(contact => {
                    const li = document.createElement('li');
                    const deleteBtn = document.createElement('button');
                    const editBtn = document.createElement('button');

                    li.style.marginTop = "5px";
                    editBtn.textContent = 'Redaguoti';
                    deleteBtn.style.marginLeft = "5px";
                    deleteBtn.textContent = 'Trinti';

                    editBtn.onclick = () => toggleEdit(contact);
                    deleteBtn.onclick = () => deleteContact(contact.id, li);

                    li.textContent = contact.name + " (" + contact.email + ") ";
                    li.appendChild(editBtn);
                    li.appendChild(deleteBtn);
                    list.appendChild(li);
                    });
                })
                .catch(error => {
                    console.error('Klaida gaunant kontaktus:', error);
                });
            }

            function toggleEdit(data) {
                let contactsList = document.querySelector('#contacts-list');
                let form = document.querySelector('#edit-form');
                let fname = document.querySelector("#fname");
                let email = document.querySelector("#email");
                if(form.style.display === 'none') {
                    contactsList.style.display = 'none';
                    form.style.display = 'block';
                    fname.value = data.name;
                    email.value = data.email;
                    document.querySelector('#submitBtn').onclick = ()=> editContact(data.id, fname.value, email.value);
                } else {
                    fname.value = null;
                    email.value = null;
                    contactsList.style.display = 'block';
                    form.style.display = 'none';
                }
            }

            function editContact(id, name, email) {
                fetch(`http://localhost:8000/api/contacts/${id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({name, email})
                })
                .then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        console.error('Nepavyko pakeisti kontakto:', response.status);
                    }
                })
                .catch(error => {
                    console.error('Klaida keitimo metu:', error);
                });
            }

            function deleteContact(id, listItem) {
                console.log(id)
                fetch(`http://localhost:8000/api/contacts/${id}`, {
                    method: 'DELETE'
                })
                .then(response => {
                    if (response.ok) {
                        listItem.remove();
                        console.log(`Kontaktas ${id} ištrintas.`);
                    } else {
                        console.error('Nepavyko ištrinti kontakto:', response.status);
                    }
                })
                .catch(error => {
                    console.error('Klaida trynimo metu:', error);
                });
            }

            indexContacts();
        </script>
    </body>
</html>
