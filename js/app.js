document.addEventListener('DOMContentLoaded', function() {
    const itemsTableBody = this.getElementById('itemsTableBody');

    // Función para cargar las películas en la tabla
    function loadItems() {
        fetch('http://localhost/Proyecto_Final_24145/api/api.php')
            .then(response => response.json())
            .then(data => {
                itemsTableBody.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(pelicula => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${pelicula.id}</td>
                            <td>${pelicula.titulo}</td>
                            <td>${pelicula.fecha_lanzamiento}</td>
                            <td>${pelicula.genero}</td>
                            <td>${pelicula.duracion}</td>
                            <td>${pelicula.director}</td>
                            <td>${pelicula.reparto}</td>
                            <td>${pelicula.sinopsis}</td>
                            <td>${pelicula.imagen}</td>                     
                            <td>
                                <button class="btn btn-danger" onclick="deleteItem(${pelicula.id})">Eliminar</button>
                            </td>
                            <td>
                                <button class="btn btn-success" onclick="editItem(
                                    ${pelicula.id}, 
                                    '${pelicula.titulo}', 
                                    '${pelicula.fecha_lanzamiento}', 
                                    '${pelicula.genero}', 
                                    '${pelicula.duracion}', 
                                    '${pelicula.director}', 
                                    '${pelicula.reparto}',
                                    '${pelicula.sinopsis}',
                                    '${pelicula.imagen}')">Editar</button>
                            </td>
                        `;
                        itemsTableBody.appendChild(row);
                    });
                } else {
                    console.error('No se encontraron películas');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Función para borrar una película
    function deleteItem(id) {
        fetch(`http://localhost/Proyecto_Final_24145/api/api.php?id=${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la solicitud');
            }
            return response.json();
        })
        .then(data => {
            console.log(data); // Para ver el mensaje de la API
            loadItems(); // Recarga los elementos después de la eliminación
        })
        .catch(error => console.error('Error:', error));
    }

    window.editItem = function(id, titulo, fecha_lanzamiento, genero, duracion, director, reparto, sinopsis, imagen) {
        document.getElementById('id').value = id;
        document.getElementById('titulo').value = titulo;
        document.getElementById('fecha_lanzamiento').value = fecha_lanzamiento;
        document.getElementById('genero').value = genero;
        document.getElementById('duracion').value = duracion;
        document.getElementById('director').value = director;
        document.getElementById('reparto').value = reparto;
        document.getElementById('sinopsis').value = sinopsis;
        document.getElementById('imagen').value = imagen;
    }

    window.deleteItem = deleteItem;
    loadItems();

    const form = document.getElementById("itemForm");
    
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(form);

        const id = formData.get('id');
        const data = {
            titulo: formData.get('titulo'),
            fecha_lanzamiento: formData.get('fecha_lanzamiento'),
            genero: formData.get('genero'),
            duracion: formData.get('duracion'),
            director: formData.get('director'),
            reparto: formData.get('reparto'),
            sinopsis: formData.get('sinopsis')
        };

        let method = 'POST';
        let url = 'http://localhost/Proyecto_Final_24145/api/api.php';

        if (id) {
            method = 'PUT';
            url += `?id=${id}`;
            data.id = id;  // Agrega el id a los datos solo en caso de actualización
        }

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.id) {
                alert("Libro guardado con éxito");
                form.reset();
                loadItems(); // Recarga los elementos después de guardar
            } else {
                alert("Error al guardar la película");
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    });
});
