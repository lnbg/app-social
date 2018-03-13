@extends('app')
@section('app')
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Add new Page</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <form action="/facebook/add-new-page" method="POST">
            {{ csrf_field() }}
            <input type="text" class="form-control" style="width: 400px; display: inline-block;" name="page_link" id="page_link" placeholder="Link to page" />
            <input type="submit" style="display: inline-block; margin-top: -5px; padding-left: 10px; padding-right: 10px;" class="btn btn-primary" value="Add">
        </form>
    </div>
</div>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Page</h3>
        <div class="box-tools">
            <!-- This will cause the box to be removed when clicked -->
            <a href="/facebook/analytics-fanpage" class="btn btn-box-tool" title="Fetching"><i class="fa fa-download"></i> Analytics</a>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <?php $arrayColors = ['bg-yellow', 'bg-aqua', 'bg-green', 'bg-red']; ?>
        @if (isset($allFacebookAnalytics))
            @foreach ($allFacebookAnalytics as $facebookAnalytics)
            <div class="col-md-4">
                <!-- Widget: user widget style 1 -->
                <div class="box box-widget widget-user-2">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <?php $colorIndexRand = rand(0, 3) ?>
                <div class="widget-user-header {{ $arrayColors[$colorIndexRand] }}">
                    <div class="widget-user-image">
                        <img class="img-circle" src="{{ $facebookAnalytics->account_picture }}" alt="{{ $facebookAnalytics->account_name }}">
                    </div>
                        <!-- /.widget-user-image -->
                        <h3 class="widget-user-username">{{ $facebookAnalytics->account_name }}</h3>
                        <a style="color: white; display: block;" href="{{ $facebookAnalytics->account_link }}"><h5 class="widget-user-desc" style="word-break: break-all; font-size: 13px;">{{ $facebookAnalytics->account_link }}</h5></a>
                    </div>
                    <div class="box-footer no-padding">
                        <ul class="nav nav-stacked">
                            <li><a href="#">Total Posts: <span class="pull-right badge bg-blue">{{ number_format($facebookAnalytics->total_posts) }}</span></a></li>
                            <li><a href="#">Total Followers <span class="pull-right badge bg-aqua">{{ number_format($facebookAnalytics->total_followers) }}</span></a></li>
                            <li><a href="#">Total Likes <span class="pull-right badge bg-green">{{ number_format($facebookAnalytics->total_likes) }}</span></a></li>
                            <li><a href="#">Average Posts/Day <span class="pull-right badge bg-red">{{ $facebookAnalytics->average_posts_per_day }}</span></a></li>
                            <li><a href="#">Average Likes/Post <span class="pull-right badge bg-yellow">{{ number_format($facebookAnalytics->average_likes_per_post) }}</span></a></li>
                        </ul>
                    </div>
                </div>
            <!-- /.widget-user -->
            </div>
            @endforeach
        @endif
    </div>
</div>
<!-- /.box -->
@endsection