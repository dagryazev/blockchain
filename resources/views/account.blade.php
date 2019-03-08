@section( 'title', 'Личный кабинет' )

@section( 'content' )
{{$data['user']}}
<div class="row">
  <h1 class="col-12">Wallets</h1>
  <a href="#" onclick="fetch(`/api/wallet/create/`);">Create a new wallet</a><br />
  @foreach ($data['wallet'] as $wallet)
    <div class="card" style="width: 100%; margin: 10px;">
      <div class="card-body">
        <h5 class="card-title"><b>{{$wallet->hash}}</b></h5>
        <p class="card-text">
          Coins: <b>{{$wallet->balance}}</b><br />
          Coins_delegate: <b>{{$wallet->balance_delegate}}</b>
        </p>
        <a href="/account/wallet/{{$wallet->id}}" class="btn btn-primary">Details</a>
      </div>
    </div>
  @endforeach
</div>
@endsection

@include( 'layouts.header' )
