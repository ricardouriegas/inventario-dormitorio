<?php
include 'db.php';

// Obtener todos los ítems
$sql = "SELECT * FROM Items";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario del Cuarto</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        #itemModal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.5);
        }

        #itemModal .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Inventario del Cuarto</h1>

    <!-- Barra de búsqueda -->
    <div class="mb-4">
        <input id="searchInput" type="text" placeholder="Buscar ítem..." class="w-full p-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <button id="addItemBtn" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Agregar Ítem</button>

    <table class="min-w-full bg-white rounded-lg shadow">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="py-2 px-4 text-left">Nombre</th>
                <th class="py-2 px-4 text-left">Descripción</th>
                <th class="py-2 px-4 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody id="itemsTable">
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="border-b" data-id="<?php echo $row['id_item']; ?>" class="item-row">
                    <td class="py-2 px-4"><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td class="py-2 px-4"><?php echo htmlspecialchars($row['descripcion']); ?></td>
                    <td class="py-2 px-4 text-center">
                        <button class="editItemBtn text-blue-500">Editar</button>
                        <button class="deleteItemBtn text-red-500">Eliminar</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal para agregar/editar ítem -->
<div id="itemModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center">
    <div class="bg-white p-6 rounded-lg shadow-md w-96">
        <h2 id="modalTitle" class="text-xl font-bold mb-4">Agregar Ítem</h2>
        <form id="itemForm">
            <input type="hidden" id="itemId" name="itemId">
            <div class="mb-4">
                <label class="block text-gray-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" required class="w-full border-gray-300 rounded mt-1">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Descripción</label>
                <textarea name="descripcion" id="descripcion" required class="w-full border-gray-300 rounded mt-1"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
                <button type="button" id="closeModalBtn" class="ml-4 text-gray-700">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const itemModal = document.getElementById('itemModal');
    const modalTitle = document.getElementById('modalTitle');
    const itemForm = document.getElementById('itemForm');
    const itemIdInput = document.getElementById('itemId');
    const addItemBtn = document.getElementById('addItemBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const itemsTable = document.getElementById('itemsTable');
    const searchInput = document.getElementById('searchInput');

    const openModal = (title, id = '', nombre = '', descripcion = '') => {
        modalTitle.textContent = title;
        itemIdInput.value = id;
        document.getElementById('nombre').value = nombre;
        document.getElementById('descripcion').value = descripcion;
        itemModal.classList.remove('hidden');
    };

    const closeModal = () => {
        itemModal.classList.add('hidden');
    };

    // Agregar ítem
    addItemBtn.addEventListener('click', () => openModal('Agregar Ítem'));

    // Editar ítem
    itemsTable.addEventListener('click', (e) => {
        if (e.target.classList.contains('editItemBtn')) {
            const row = e.target.closest('tr');
            const id = row.dataset.id;
            const nombre = row.children[0].textContent;
            const descripcion = row.children[1].textContent;
            openModal('Editar Ítem', id, nombre, descripcion);
        }
    });

    // Guardar ítem (agregar/editar)
    itemForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const formData = new FormData(itemForm);
    const isEdit = formData.get('itemId');

    fetch(isEdit ? './backend/edit.php' : './backend/add.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Éxito',
                text: `Ítem ${isEdit ? 'editado' : 'agregado'} correctamente`,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                // Recargar después de que el usuario confirme
                location.reload();
            });
            closeModal();
            } else {
                Swal.fire('Error', 'Hubo un problema al guardar el ítem', 'error');
            }
        });
    });


    // Cerrar modal
    closeModalBtn.addEventListener('click', closeModal);

    // Eliminar ítem
    itemsTable.addEventListener('click', (e) => {
        if (e.target.classList.contains('deleteItemBtn')) {
            const row = e.target.closest('tr');
            const id = row.dataset.id;

            Swal.fire({
                title: '¿Estás seguro?',
                text: 'No podrás deshacer esta acción',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`./backend/delete.php?id=${id}`, { method: 'GET' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Eliminado', 'El ítem ha sido eliminado', 'success');
                            row.remove();
                        } else {
                            Swal.fire('Error', 'Hubo un problema al eliminar el ítem', 'error');
                        }
                    });
                }
            });
        }
    });

    // Filtro de búsqueda
    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        const rows = itemsTable.querySelectorAll('tr');

        rows.forEach(row => {
            const nombre = row.children[0].textContent.toLowerCase();
            const descripcion = row.children[1].textContent.toLowerCase();
            const matchesSearch = nombre.includes(searchTerm) || descripcion.includes(searchTerm);
            row.style.display = matchesSearch ? '' : 'none';
        });
    });
});
</script>

</body>
</html>
