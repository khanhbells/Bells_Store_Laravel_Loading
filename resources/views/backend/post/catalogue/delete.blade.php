@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('post.catalogue.destroy', $postCatalogue->id) }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">{{ __('message.generalTitle') }}</div>
                    <div class="panel-description">
                        <p class="text-danger">{{ __('message.generalDescription') }}
                            <strong>{{ $postCatalogue->name }}</strong>
                        </p>
                        <p class="text-danger"><strong>{{ __('message.danger') }}</strong></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">{{ __('message.tableName') }}
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="name" name="name"
                                        value="{{ old('name', $postCatalogue->name ?? '') }}" class="form-control"
                                        placeholder="" autocomplete="off" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button class="btn btn-danger" type="submit" name="send"
                value="send">{{ __('message.deleteButton') }}</button>
        </div>
    </div>
</form>
