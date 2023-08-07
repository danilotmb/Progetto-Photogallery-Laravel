
@extends('templates.admin')
@section('content')
    <h1>Users</h1>
    <table class="table table-striped data-table" id="users-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>NAME</th>
            <th>EMAIL</th>
            <th>CREATED AT</th>
            <th>DELETED</th>
            <th>ACTIONS</th>
        </tr>
        </thead>
        <tbody>


        </tbody>

    </table>
@endsection
@section('footer')
    @parent
    <script>

      var dataTable =  $('#users-table').DataTable({
            processing: true,
            paging: true,
            serverSide: true,
            ajax: '{{route('admin.getUsers')}}',
            columns: [

                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'created_at', name: 'created'},
                {data: 'deleted_at', name: 'del'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });


                    $('#users-table').on('click', '.ajax', function (ele) {
                    ele.preventDefault();
                    const isDelete = this.id.indexOf('delete')>=0;
                    const msg = isDelete ? 'Do you really want to delete this record' : 'Do you really want to restore this record?';
                    if(!confirm(msg)){
                        return false;
                    }

                    var urlUsers =   $(this).attr('href');

                    var tr =this.parentNode.parentNode;
                    console.log(tr)
                    $.ajax(
                        urlUsers,
                        {
                            method: isDelete ? 'DELETE' : 'PATCH',
                            data : {
                                '_token' : window.Laravel.csrf_token

                            },
                            complete : function (resp) {
                                console.log(resp);
                                if(resp.responseText == 1) {
                                    if(urlUsers.endsWith('hard=1'))
                                    {
                                        tr.parentNode.removeChild(tr);
                                    }
                                     dataTable.ajax.reload();
                                     alert(isDelete ? 'User deleted correctly' : 'User restored correctly');
                                        

                                } else {
                                    alert('Problem contacting server');
                                }
                            }
                        }
                    )
            });

        
    </script>
@endsection
