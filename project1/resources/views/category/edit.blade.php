@extends('layoutNV.admin')

@section('title')
    <title>Edit Category</title>
@endsection

@section('content')
    <main class="content">
    <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Edit Category</strong></h1>
        <div class="row">
            <div class="col-md-6">
                <form action="" method="post">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="cat_name" class="form-control" placeholder="Enter Category Name" value="{{ $cat[0]->name }}">
                        @error('cat_name')
                        <small style="color: red">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parents Category</label>
                        <select class="form-control" name="parent_id" >
                            <option value="null">Please select parent category</option>
                            {!! $htmlSelect !!}
                        </select>
                        @error('parent_id')
                        <small style="color: red">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                       <button type="submit" class="btn btn-primary">Update</button>
                       <a href="{{ route('category.list') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
    </main>
@endsection

