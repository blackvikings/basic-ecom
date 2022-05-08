@extends('welcome')
@section('content')

    <div class="col-md-12 mt-5">
        <button data-bs-toggle="modal" data-bs-target="#createModal" class="btn btn-primary">Add Product</button>
    </div>
    <div class="col-md-12 mt-5">
        <table id="product-table" class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Serial no.</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <form action="javascript:void(0)" accept-charset="utf-8" method="POST" id="add-product" enctype="multipart/form-data">
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="md-3">
                                <label class="form-label" for="addProductName">Name</label>
                                <input type="text" name="name" id="addProductName" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-3">
                                <label class="form-label" for="addProductPrice">Price</label>
                                <input type="number" name="price" id="addProductPrice" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="md-3">
                                <label for="addProductDescription" class="form-label">Description</label>
                                <textarea name="description" id="addProductDescription" class="form-control" ></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="md-3 mt-2">
                                <label for="addImageFiles" class="form-label">Select Images</label>
                                <input type="file" name="images[]" id="addImageFiles" placeholder="Choose Files" multiple>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
        </form>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <form id="edit-product" action="javascript:void(0)" accept-charset="utf-8" method="POST" enctype="multipart/form-data">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="md-3">
                                    <label class="form-label" for="editProductName">Name</label>
                                    <input type="text" name="editProductName" id="editProductName" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="md-3">
                                    <label class="form-label" for="editProductPrice">Price</label>
                                    <input type="number" name="editProductPrice" id="editProductPrice" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="md-3">
                                    <label for="editProductDescription" class="form-label">Description</label>
                                    <textarea name="editProductDescription" id="editProductDescription" class="form-control" ></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="md-3 mt-2">
                                    <label for="editImageFiles" class="form-label">Select Images</label>
                                    <input type="file" name="editImageFiles[]" id="editImageFiles" placeholder="Choose Files" multiple>
                                    <div id="uploded-images"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script>
        // $(function() {
           var table = $('#product-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('products.index') !!}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'product_name', name: 'name' },
                    { data: 'product_price', name: 'price' },
                    { data: 'action', name: 'action' },
                ]
           });
           $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
           });
           $('#edit-product').submit(function (e) {
                e.preventDefault();
                let TotalFiles = $('#editImageFiles')[0].files.length; //Total files
                let files = $('#editImageFiles')[0];
                let formData = new FormData(document.getElementById('edit-product'));

                console.log("images before upload: "+files);
                for (let i = 0; i < TotalFiles; i++) {
                    formData.append('images' + i, files.files[i]);
                }
                formData.append('TotalFiles', TotalFiles);
                let productId = $('#productId').val();

               for (var [key, value] of formData.entries()) {
                   console.log(key, value);
               }
                $.ajax({
                    url: "{{ url('products') }}/"+productId,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache:false,
                    dataType: 'json',
                    success: (data) => {
                        console.log(data);
                        this.reset();
                        table.ajax.reload();
                        $('#uploded-images').html();
                        $('#editModal').modal('hide');
                        swal("Poof! Your imaginary file has been saved!", {
                            icon: "success",
                        });
                    },
                    error: function(data){
                        alert(data.responseJSON.errors.files[0]);
                        console.log(data.responseJSON.errors);
                    }
                });

            });
           $('#add-product').submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                let TotalFiles = $('#addImageFiles')[0].files.length; //Total files
                let files = $('#addImageFiles')[0];
                // console.log("images before upload: "+files);
                for (let i = 0; i < TotalFiles; i++) {
                    formData.append('images' + i, files.files[i]);
                }
                formData.append('TotalFiles', TotalFiles);

                for (var [key, value] of formData.entries()) {
                    console.log(key, value);
                }

                $.ajax({
                    type:'POST',
                    url: "{{ route('products.store') }}",
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: (data) => {
                        console.log(data);
                        this.reset();
                        table.ajax.reload();
                        $('#createModal').modal('hide');
                        swal("Poof! Your imaginary file has been saved!", {
                            icon: "success",
                        });
                    },
                    error: function(data){
                        alert(data.responseJSON.errors.files[0]);
                        console.log(data.responseJSON.errors);
                    }
                });

            });

        // });

        function deleteProduct(id) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type:'DELETE',
                            url:"{{ url('products') }}/"+id,
                            data:{id:id},
                            success:function(data){
                                swal("Poof! Your imaginary file has been deleted!", {
                                    icon: "success",
                                });
                                table.ajax.reload();
                            }
                        });

                    } else {
                        swal("Your imaginary file is safe!");
                    }
                });

        }

        function editProduct(id) {
            $.ajax({
                type:'GET',
                url: "{{ url('products') }}/"+id+"/edit",
                cache:false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: (data) => {
                    $('#uploded-images').html();
                    document.getElementById('editProductName').value = data.product.name;
                    document.getElementById('editProductDescription').value = data.product.description;
                    document.getElementById('editProductPrice').value= data.product.price;
                    for (var g = 0; g < data.product.images.length; g++){
                        $('#uploded-images').append('<input type="text" style="display: none;" id="productId" value="'+data.product.id+'" name="id"/>')
                        $('#uploded-images').append('<button class="btn btn-danger" type="button" id="btn-delete'+g+'" onclick="removeImage('+g+')">Delete</button>')
                        $('#uploded-images').append('<img src="'+data.product.images[g]+'" id="image'+g+'" style="width: 100%;" />');
                        $('#uploded-images').append('<input type="text" style="display: none" value="'+data.product.images[g]+'"  id="imageAdd'+g+'" name="imagePaths[]" />')
                    }
                    $('#editModal').modal('show');
                },
                error: function(data){
                    console.log(data.responseJSON.errors);
                    alert(data.responseJSON.errors.files[0]);
                }
            });
        }

        function removeImage(id) {
            $("#btn-delete"+id).remove();
            $("#image"+id).remove();
            $("#imageAdd"+id).remove();
        }
    </script>
@endpush
