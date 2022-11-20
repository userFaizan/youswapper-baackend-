@extends('master');
@section('content');
<main role="main" class="main-content">
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-12">
              <div class="row align-items-center my-4">
                <div class="col">
                  <h2 class="h3 mb-0 page-title">Products</h2>
                </div>
                <!-- <div class="col-auto"> -->
                  <!-- <button type="button" class="btn btn-secondary"><span class="fe fe-trash fe-12 mr-2"></span>Delete</button> -->
                <!-- <a href="{{ route('userindex') }}">  <button type="button" class="btn btn-primary"><span class="fe fe-filter fe-12 mr-2"></span>Create</button></a>
                </div> -->
              </div>
              <!-- table -->
              <div class="card shadow">
                <div class="card-body">
                  <table class="table table-borderless table-hover" id="myTable">
                  <thead>
                          <tr> 
                            <th>ID</th>
                            <th>Product_name</th>
                            <th>discription</th>
                            <th>Product_image</th> 
                            <th>Action</th>
                          </tr>
                        </thead>
                    <!-- <tbody>
                      @foreach($data as $user)
                      <tr>
                      <td>{{$user->id}}</td>
                      <td>{{$user->name}}</td>
                      <td>{{$user->email}}</td>
                      </tr>
                    </tbody> -->
                    <!-- @endforeach -->
                    <tbody>
                      @foreach($data as $user)
                          <tr>
                            <td>
                             {{$user->id}}
                            </td>
                            <td>
                              {{$user->product_name}}
                            </td>
                            <td>
                              {{$user->description}}
                            </td>
                           
                            <td>
                              @if($user->images)
                              @foreach($user->images as $image)
                              <img src="{{ asset('storage/' .$image->image) }}" height="50px" width="50px">
                              @endforeach
                              @endif
                            
                            </td>
                            
                            <td>
                            <a href="{{route('product.del', $user->id)}}">  <button type="button" class="btn btn-danger"><span class=""></span>Delete</button></a>
                            </td>
                          </tr>
                          @endforeach
                        </tbody>
                  </table>
                </div>
              </div>
            </div> <!-- .col-12 -->
          </div> <!-- .row -->
        </div> <!-- .container-fluid -->
      </main> <!-- main -->
    </div> <!-- .wrapper -->

   @endsection
  