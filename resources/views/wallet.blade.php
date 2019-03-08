@section( 'title', 'Кошелек' )

@section( 'content' )
<div class="row">
  <h1 class="col-12 align-left">{{$data['wallet']->hash}}</h1>
  <p><b>Balance:</b> {{$data['wallet']->balance}} coins</p>
  <p><b>Balance delegate:</b> {{$data['wallet']->balance_delegate}} coins</p>
  <p><b>Created at:</b> {{$data['wallet']->created_at}}</p>
</div>
<div class="row">
  <h1>Delegate to server</h1>
  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <label class="input-group-text" for="inputGroupSelect01">Servers</label>
    </div>
    <select class="custom-select" id="inputServers">
      <option selected>Choose...</option>
      @foreach ($data['servers'] as $server)
        <option value="{{$server->id}}">{{$server->title}}</option>
      @endforeach
    </select>
  </div>
  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text">$</span>
    </div>
    <input type="text" class="form-control" id="coinsDelegate" aria-label="Amount (to the nearest dollar)">
    <div class="input-group-append">
      <span class="input-group-text">coins</span>
    </div>
  </div>
  <a onclick="DelegateToServer()" class="btn btn-primary">Delegate</a><br />
</div>
<div class="row">
  <h1>Payment</h1>
  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text">$</span>
    </div>
    <input type="text" class="form-control" id="coins" aria-label="Amount (to the nearest dollar)">
    <div class="input-group-append">
      <span class="input-group-text">coins</span>
    </div>
  </div>
  <a onclick="fetch(`/api/wallet/payment/in/{{$data['wallet']->id}}/${document.getElementById('coins').value}`);location.reload()" class="btn btn-primary">Payment</a><br />
</div>
<div class="row">
  <h2 style="margin-bottom: 30px;">История транзакций</h2>
  @foreach ($data['transaction'] as $transaction)
    @switch($transaction->type)
      @case('put')
      <div class="alert alert-primary" role="alert">
        Пополнение кошелька  - <b>{{$transaction->value}}</b> coins
      </div>
          @break

      @case('delegate_to')
        <div class="alert alert-success" role="alert">
          Делегирование на <i>{{$transaction->from_server}}</i>  - <b>{{$transaction->value}}</b> coins
        </div>
          @break

      @case('delegate_from')
        <div class="alert alert-secondary" role="alert">
          Анделигирование от <i>{{$transaction->from_server}}</i>  - <b>{{$transaction->value}}</b> coins
        </div>
          @break

      @case('out')
        <div class="alert alert-danger" role="alert">
          Вывод денег  - <b>{{$transaction->value}}</b> coins
        </div>
          @break

      @case('reward')
        <div class="alert alert-info" role="alert">
          Ежедневная награда  - <b>{{$transaction->value}}</b> coins
        </div>
          @break


      @default
          Default case...
    @endswitch
  @endforeach
</div>
@endsection
<script type="text/javascript">
function DelegateToServer(){
  let inputServers  = document.getElementById("inputServers"),
      coins         = document.getElementById("coinsDelegate").value
  fetch(`/api/wallet/delegate/{{$data['wallet']->id}}/${inputServers.options[inputServers.selectedIndex].value}/${coins}`)

}
</script>
@include( 'layouts.header' )
