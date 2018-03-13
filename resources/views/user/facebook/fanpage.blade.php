@extends('app')
@section('app')
   @if (isset($allFacebookAnalytics) && count($allFacebookAnalytics) > 0)
        <pre />
        <?php var_dump($allFacebookAnalytics); ?>
   @endif
   @if (isset($response))
        <?php dd($response) ?>
   @endif
    <form action="/facebook/fetch-fanpage" method="POST">
        {{ csrf_field() }}
        <input type="text" name="name" id="name" placeholder="fanpage name" />
        <input type="submit" value="Fetch">
    </form>
@endsection