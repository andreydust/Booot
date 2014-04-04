    <aside id="OrderForm">
      <div class="container">
        <h2>Сделайте заказ прямо сейчас</h2>
        <form class="form-inline" role="form" action="/basket/fastOrder" method="post" id="FastOrderForm">
          <div class="form-group">
            <label class="sr-only" for="OrderFormPhone">Телефон</label>
            <input type="tel" class="form-control" id="OrderFormPhone" placeholder="Телефон" name="phone">
          </div>
          <div class="form-group">
            <label class="sr-only" for="OrderFormOrder">Название товара, услуги и комментарий к заказу</label>
            <input 
              type="text" 
              class="form-control" 
              id="OrderFormOrder" 
              placeholder="Название товара, услуги и комментарий к заказу" 
              name="order"
              value="<?=isset(giveObject('Catalog')->product['name'])?trim(giveObject('Catalog')->product['product_singular_name'].' '.giveObject('Catalog')->product['brand_name'].' '.giveObject('Catalog')->product['name']):''?>">
            <input
              type="hidden" 
              name="productId" 
              id="OrderFormProductId" 
              value="<?=isset(giveObject('Catalog')->product['id'])?giveObject('Catalog')->product['id']:0?>">
          </div>
          <button type="submit" class="btn btn-default">Отправить</button>
        </form>
      </div>
    </aside>