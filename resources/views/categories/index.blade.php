@extends('templates.default')
@section('content')

@if(session()->has('message'))
    <x-alert-info>{{session()->get('message')}}</x-alert-info>
@endif

<div class="row">
    <div class="col-sm-8">
        <h1 class="display-4">Category List</h1>
        <table class="table table-hover" id="categoryList">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th>Albums</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                    <tr id="tr-{{$cat->id}}">
                        <td>{{$cat->id}}</td>
                        <td id="catid-{{$cat->id}}">{{$cat->category_name}}</td>
                        <td>{{$cat->created_at->format('Y-m-d H:i')}}</td>
                        <td>{{$cat->updated_at->format('Y-m-d H:i')}}</td>
                        <td>
                            @php
                                $userAlbumCount = $cat->albums()
                                    ->where('user_id', auth()->id()) // Filtra solo gli album dell'utente loggato
                                    ->count();
                            @endphp
                        
                            @if($userAlbumCount > 0)
                                <a class="btn btn-success" href="{{ route('albums.index') }}?category_id={{ $cat->id }}">
                                    {{ $userAlbumCount }}
                                </a>
                            @else
                                {{ $userAlbumCount }}
                            @endif
                        </td>
                        <td class="text-center" style="padding-left: 20px;">
                            <a id="upd-{{$cat->id}}" class="btn btn-outline-info m-1" href="{{ route('categories.edit', $cat->id) }}" title="UPDATE CATEGORY">
                                <i class="bi bi-pen"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $cat->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <button id="btnDelete-{{$cat->id}}" class="btn btn-danger m-1" title="DELETE CATEGORY">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tfoot>
                        <tr>
                            <th colspan="5">No categories</th>
                        </tr>
                    </tfoot>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="col-sm-4">
        @include('categories.categoryform')
    </div>
</div>
@endsection


@section('footer')
   @parent
   <script>
    $('document').ready(function () {
        const categoryUrl = '{{ route('categories.store') }}';
        $('div.alert').fadeOut(5000);
        $('form .btn-danger ').on('click',function (ele) {
            ele.preventDefault();

            var f = this.parentNode;
            var categoryId = this.id.replace('btnDelete-','')*1;
            var Trid ='tr-'+ categoryId;
            var urlCategory = f.action;

            $.ajax(
                urlCategory,
                {
                    method: 'DELETE',
                    data : {
                        '_token' : window.Laravel.csrf_token
                    },
                    complete : function (resp) {
                        var response = JSON.parse(resp.responseText);
                        alert(response.message);
                        if(response.success){
                            //  alert(resp.responseText)
                            $('#'+Trid).fadeOut();
                            dataTable.ajax.reload();

                        } else {
                            alert('Problem contacting server');
                        }
                    }
                }
            )
        });

            // add Category ajax
            $('#manageCategoryForm .btn-primary ').on('click',function (ele) {

            ele.preventDefault();
            var f = $('#manageCategoryForm');
            console.log(f);
            var data  = f.serialize();
            var urlCategory = f.attr('action');

            $.ajax(
                urlCategory,
                {
                    method: 'POST',
                    data : data

                }
            ).done(response => {

                $('#methodType').remove();
                selectedCategory = null;
                f[0].action = categoryUrl;
                alert(response.message);
                if(response.success){
                    f[0].category_name.value = '';
                    f[0].reset();
                    dataTable.ajax.reload();
                } else {
                    alert('Problem contacting server');
                }
            })
            });


            
            // update category ajax
            // add Category ajax
            const f = $('#manageCategoryForm');
            let selectedCategory = null;
            f[0].category_name.addEventListener('keyup', function(){
                if(selectedCategory){
                    selectedCategory.text( f[0].category_name.value);
                }

            });

            $('#categoryList a.btn-outline-info').on('click',function (ele) {

                ele.preventDefault();
                var categoryId = this.id.replace('upd-','')*1;

                var catRow = $('#tr-' +categoryId);
                $('#categoryList tr').css('border','0px');
                catRow.css('border', '2px solid green');
                var urlUpdate  =this.href.replace('/edit','');
                var tdCat =$('#catid-' + categoryId);
                selectedCategory = tdCat;
                var category_name = tdCat.text();
                var f = $('#manageCategoryForm');
                f.attr('action',urlUpdate);
                f[0].category_name.value = category_name;
                const inputT = document.querySelector('#methodType');
                if(!inputT){
                var input = document.createElement('input');

                    input.name = '_method';
                    input.id ='methodType'
                    input.type = "hidden";
                    input.value = 'PATCH';
                    f[0].appendChild(input);
                }

            });
        });


        var cssLink = $('<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" type="text/css" />');

        var jsScript = $(<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js">);
            
        
        </script>

        
               
                    
            <div class="col-md-8 offset-md-2 d-flex justify-content-center">
                 {{$categories->links('vendor.pagination.bootstrap-5')}}
            </div>
                    
  
@endsection
