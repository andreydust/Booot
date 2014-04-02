    <aside id="OrderForm">
      <div class="container">
        <h2>Сделайте заказ прямо сейчас</h2>
        <form class="form-inline" role="form" action="/basket/fastOrder" method="post" id="FastOrderForm">
          <div class="form-group">
            <label class="sr-only" for="OrderFormName">Ваше имя</label>
            <input type="text" class="form-control" id="OrderFormName" placeholder="Ваше имя" name="name">
          </div>
          <div class="form-group">
            <label class="sr-only" for="OrderFormPhone">Телефон или email</label>
            <input type="tel" class="form-control" id="OrderFormPhone" placeholder="Телефон или email" name="phone">
          </div>
          <div class="form-group">
            <label class="sr-only" for="OrderFormOrder">Название товара, услуги и комментарий к заказу</label>
            <input type="text" class="form-control" id="OrderFormOrder" placeholder="Название товара, услуги и комментарий к заказу" name="order">
            <input type="hidden" name="productId" id="OrderFormProductId" value="0">
          </div>
          <button type="submit" class="btn btn-default">Отправить</button>
        </form>
      </div>
    </aside>