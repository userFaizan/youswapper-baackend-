@extends('master');
@section('content');
<main role="main" class="main-content">
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-12">
              <div class="row align-items-center my-4">
                <div class="col">
                  <h2 class="h3 mb-0 page-title">Users</h2>
                </div>
                <div class="col-auto">
                  <!-- <button type="button" class="btn btn-secondary"><span class="fe fe-trash fe-12 mr-2"></span>Delete</button> -->
                <a href="{{ route('userindex') }}">  <button type="button" class="btn btn-primary"><span class="fe fe-filter fe-12 mr-2"></span>Create</button></a>
                </div>
              </div>
              <!-- table -->
              <div class="card shadow">
                <div class="card-body">
                  <table class="table table-borderless table-hover" id="myTable">
                  <thead>
                          <tr> 
                            <th>ID</th>
                            <th>name</th>
                            <th>email</th>
                            <th>Address</th>
                            <th>Lat</th>
                            <th>Lng</th>
                            <th>Registration Date</th>
                            <th>Avatar</th>
                            <th>Action</th>
                          </tr>
                        </thead>
            
                    <tbody>
                      @foreach($data as $user)
                          <tr>
                            <td>
                             {{$user->id}}
                            </td>
                            <td>
                              {{$user->name}}
                            </td>
                            <td>
                              {{$user->email}}
                            </td>
                            <td>
                              {{$user->address}}
                            </td>
                            <td>
                              {{$user->lat}}
                            </td>
                            <td>
                              {{$user->lng}}
                            </td>
                            <td>
                              {{$user->created_at->format('Y-m-d') 
                            }}
                            </td>
                            <td>
                            <img src="{{ asset('storage/' .$user->avatar) }}" height="50px" width="50px">

                            </td>
                          
                            <td>
                             <a href="{{route('user.del', $user->id)}}">  <button type="button" class="btn btn-info"><span class=""></span>Delete</button></a>
                             <a href="{{route('users_edit', $user->id)}}">  <button type="button" class="btn btn-primary"><span class=""></span>Edit</button></a>
                             @if ($user->status == 1)
                             <a href="{{route('update_status',[$user->id, 'status_code'=> 0] )}}">  <button type="button" class="btn btn-danger"><span class=""></span>Block</button></a>
                             @else
                             <a href="{{route('update_status',[$user->id, 'status_code'=> 1] )}}">  <button type="button" class="btn btn-success"><span class=""></span>Unblock</button></a>

                             @endif

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
  