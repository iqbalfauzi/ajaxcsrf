<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajax CRUD with Bootstrap modals and Datatables with Image Upload</title>
    <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css')?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.min.css')?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')?>" rel="stylesheet">
</head> 
<body>
    <div class="container">
        <h1 style="font-size:20pt">Ajax CRUD with CSRF Token</h1>

        <h3>Person Data</h3>
        <br />
        <button class="btn btn-success" onclick="add_person()"><i class="glyphicon glyphicon-plus"></i> Add Person</button>
        <button class="btn btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
        <br />
         <br />
        <input type="text" id="id_token2" class="form-control" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash()?>">
        <br />
         <br />
        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Address</th>
                    <th>Date of Birth</th>
                    <th>Photo</th>
                    <th style="width:150px;">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>

            <tfoot>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Address</th>
                    <th>Date of Birth</th>
                    <th>Photo</th>
                    <th>Action</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script src="<?php echo base_url('assets/jquery/jquery-2.1.4.min.js')?>"></script>
    <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js')?>"></script>
    <script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js')?>"></script>
    <script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.min.js')?>"></script>
    <script src="<?php echo base_url('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js')?>"></script>


    <script type="text/javascript">

        var save_method;
        var table;
        var base_url = '<?php echo base_url();?>';

        var name_token = "<?php echo $this->security->get_csrf_token_name() ?>";
        var key_token = "<?php echo $this->security->get_csrf_hash() ?>";

        $(document).ready(function() {

            table = $('#table').DataTable({ 

                "processing": true,
                "serverSide": true,
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('person/ajax_list')?>",
                    "type": "POST"
                },

                "columnDefs": [
                { 
                    "targets": [ -1 ], 
                    "orderable": false,
                },
                { 
                    "targets": [ -2 ],
                    "orderable": false,
                },
                ],

            });

            $('.datepicker').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true,
                orientation: "top auto",
                todayBtn: true,
                todayHighlight: true,  
            });

            $("input").change(function(){
                $(this).parent().parent().removeClass('has-error');
                $(this).next().empty();
            });
            $("textarea").change(function(){
                $(this).parent().parent().removeClass('has-error');
                $(this).next().empty();
            });
            $("select").change(function(){
                $(this).parent().parent().removeClass('has-error');
                $(this).next().empty();
            });
        });



        function add_person()
        {
            save_method = 'add';
            $('#form')[0].reset(); 
            $('.form-group').removeClass('has-error'); 
            $('.help-block').empty(); 
            $('#modal_form').modal('show'); 
            $('.modal-title').text('Add Person');

            $('#photo-preview').hide(); 

            $('#label-photo').text('Upload Photo'); 
        }

        function edit_person(id)
        {
            save_method = 'update';
            $('#form')[0].reset();
            $('.form-group').removeClass('has-error'); 
            $('.help-block').empty(); 

            $.ajax({
                url : "<?php echo site_url('person/ajax_edit')?>/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(data)
                {

                    $('[name="id"]').val(data.id);
                    $('[name="firstName"]').val(data.firstName);
                    $('[name="lastName"]').val(data.lastName);
                    $('[name="gender"]').val(data.gender);
                    $('[name="address"]').val(data.address);
                    $('[name="dob"]').datepicker('update',data.dob);
                    $('#modal_form').modal('show'); 
                    $('.modal-title').text('Edit Person'); 

                    $('#photo-preview').show();

                    if(data.photo)
                    {
                        $('#label-photo').text('Change Photo'); 
                        $('#photo-preview div').html('<img src="'+base_url+'upload/'+data.photo+'" class="img-responsive">'); 
                        $('#photo-preview div').append('<input type="checkbox" name="remove_photo" value="'+data.photo+'"/> Remove photo when saving'); 
                    }
                    else
                    {
                        $('#label-photo').text('Upload Photo');
                        $('#photo-preview div').text('(No photo)');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data from ajax');
                }
            });
        }

        function reload_table()
        {
            table.ajax.reload(null,false);
        }

        function reload_token(nama, key){
            name_token = nama;
            key_token = key;
        }

        function save()
        {
            $('#btnSave').text('saving...'); 
            $('#btnSave').attr('disabled',true);
            var url;

            if(save_method == 'add') {
                url = "<?php echo site_url('person/ajax_add')?>";
            } else {
                url = "<?php echo site_url('person/ajax_update')?>";
            }

            var formData = new FormData($('#form')[0]);
            $.ajax({
                url : url,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "JSON",
                success: function(data)
                {

                    if(data.status)
                    {
                        $('#modal_form').modal('hide');
                        $('#id_token').val(data.key_token);
                        $('#id_token2').val(data.key_token);
                        reload_token(data.name_token, data.key_token);
                        reload_table();
                    }
                    else
                    {
                        for (var i = 0; i < data.inputerror.length; i++) 
                        {
                            $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); 
                            $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]);
                        }
                    }
                    $('#btnSave').text('save'); 
                    $('#btnSave').attr('disabled',false);


                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error adding / update data');
                    $('#btnSave').text('save'); 
                    $('#btnSave').attr('disabled',false);

                }
            });
        }

        function delete_person(id)
        {
            if(confirm('Are you sure delete this data?'))
            {

                $.ajax({
                    url : "<?php echo site_url('person/ajax_delete')?>/"+id,
                    type: "POST",
                    data: name_token+'='+key_token,
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data.status)
                        {
                            $('#id_token').val(data.key_token);
                            $('#id_token2').val(data.key_token);
                            reload_token(data.name_token, data.key_token);
                            reload_table();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        alert('Error deleting data');
                    }
                });

            }else{
                alert('Error deleting data');
            }
        }
    </script>

    <!-- Bootstrap modal -->
    <div class="modal fade" id="modal_form" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Person Form</h3>
                </div>
                <div class="modal-body form">
                    <form action="#" id="form" class="form-horizontal">
                        <input type="hidden" id="id_token" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash()?>">
                        <input type="hidden" value="" name="id"/> 
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3">First Name</label>
                                <div class="col-md-9">
                                    <input name="firstName" placeholder="First Name" class="form-control" type="text" required="required">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Last Name</label>
                                <div class="col-md-9">
                                    <input name="lastName" placeholder="Last Name" class="form-control" type="text" required="required">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Gender</label>
                                <div class="col-md-9">
                                    <select name="gender" class="form-control" required="required">
                                        <option disabled selected>--Select Gender--</option>
                                        <option value="1">Male</option>
                                        <option value="2">Female</option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Address</label>
                                <div class="col-md-9">
                                    <textarea name="address" placeholder="Address" class="form-control" required="required"></textarea>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-md-3">Date of Birth</label>
                                <div class="col-md-9">
                                    <input name="dob" placeholder="yyyy-mm-dd" class="form-control datepicker" type="text" required="required">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group row" id="photo-preview">
                                <label class="control-label col-md-3">Photo</label>
                                <div class="col-md-9">
                                    (No photo)
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-md-3" id="label-photo">Upload Photo </label>
                                <div class="col-md-9">
                                    <input name="photo" type="file">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- End Bootstrap modal -->
</body>
</html>