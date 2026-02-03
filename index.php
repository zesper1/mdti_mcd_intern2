<?php 
require_once("db.php");
require_once("products_service.php");

// Fetch the data using your function
$products = getProducts($pdo);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <title>Product Management</title>
</head>
<body class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Inventory List</h1>
        <div id="loader" style="display:none;" class="spinner-border spinner-border-sm text-primary"></div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th style="width: 25%;">Price</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $product): ?>
                <tr id="row-<?= $product['id'] ?>">
                    <td><?= htmlspecialchars($product['id']) ?></td>
                    <td>
                        <input type="text" class="form-control form-control-sm name-input" 
                               value="<?= htmlspecialchars($product['product_name']) ?>">
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                            <input type="number" step="0.01" class="form-control price-input" 
                                   value="<?= htmlspecialchars($product['price']) ?>">
                        </div>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-success btn-sm btn-save" data-id="<?= $product['id'] ?>">
                            Update
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-light">
                <tr>
                    <td>New</td>
                    <td>
                        <input type="text" id="new_name" class="form-control form-control-sm" placeholder="Enter Product Name">
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                            <input type="number" step="0.01" id="new_price" class="form-control" placeholder="0.00">
                        </div>
                    </td>
                    <td class="text-center">
                        <small class="text-muted">Press Enter ↵</small>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.btn-save').on('click', function() {
                const btn = $(this);
                const row = btn.closest('tr');
                
                // Collect values from the current row
                const data = {
                    action: 'update',
                    id: btn.data('id'),
                    product_name: row.find('.name-input').val(),
                    price: row.find('.price-input').val()
                };

                // UI Feedback
                btn.prop('disabled', true).text('...');
                $('#loader').show();

                $.ajax({
                    url: 'products_controller.php', // This is your switch statement file
                    method: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            row.addClass('table-success');
                            setTimeout(() => row.removeClass('table-success'), 1000);
                        } else {
                            alert('Error: ' + (response.error || 'Unknown error'));
                        }
                    },
                    error: function() {
                        alert('Server connection failed.');
                    },
                    complete: function() {
                        btn.prop('disabled', false).text('Update');
                        $('#loader').hide();
                    }
                });
            });

            $('#new_name, #new_price').on('keypress', function(e) {
                if (e.which == 13) { // 13 is the Enter key
                    const name = $('#new_name').val();
                    const price = $('#new_price').val();

                    if (name === "") {
                        alert("Please enter a product name.");
                        return;
                    }

                    $('#loader').show();

                    $.ajax({
                        url: 'products_controller.php',
                        method: 'POST',
                        data: {
                            action: 'create',
                            product_name: name,
                            price: price
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Refresh page to show the new item in the list
                                location.reload(); 
                            } else {
                                alert('Error: ' + response.error);
                            }
                        },
                        complete: function() {
                            $('#loader').hide();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>