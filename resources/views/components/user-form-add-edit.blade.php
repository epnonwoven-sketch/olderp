<!-- Modal Form -->
<div class="modal fade modal-lg" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    @csrf
                    <!-- Hidden field for Client ID -->
                    <input type="hidden" id="id" name="id" value="">

                    <!-- Client Name -->
                    <div class="mb-3">
                        <label class="form-label" for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" maxlength="100" id="name" name="name" class="form-control" placeholder="Enter User Name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email<span class="text-danger">*</span></label>
                        <input type="email" maxlength="100" id="email" name="email" class="form-control" placeholder="user@gmail.com" required>
                    </div>
                    <div id="passwordDiv">
                        <div class="mb-3">
                            <label class="form-label" for="password">Password<span class="text-danger">*</span></label>
                            <input type="text" maxlength="100" id="password" name="password" class="form-control" placeholder="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password_confirmation">Conform Password<span class="text-danger">*</span></label>
                            <input type="text" maxlength="100" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="conformPassword Password" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <label class="control-label"><b><u>Roll Type</u></b></label>
                        </div>
                        @if($user_type_list??"")
                            @foreach ($user_type_list as $values) 
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <input type="radio" id="user_type_id{{$values['id']}}" name="user_type_id" class="magic-checkbox" value="{{$values['id']}}" required />
                                    <label for="user_type_id{{$values['id']}}">{{$values['user_type']}}</label>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div> 

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>