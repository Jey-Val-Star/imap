<div class="row main">
  <div class="col-md-2">
    <a href="/" class="btn btn-primary btn-block">Входящие</a>
    <a href="/spam" class="btn btn-primary btn-block">Спам</a>
    <a href="/trash" class="btn btn-primary btn-block">Корзина</a><br />
    <a href="/logout" class="btn btn-default btn-block">Выйти</a>
  </div>


  <div class="col-md-10 content">
    <?php if($data != false) { ?>
      
      <?php foreach($data as $row){ ?>
        <div class="row <?=(count($data)>1)?'item_list':''?>">
          <div class="col-md-12"><a href="?id=<?=$row['id']?>"><b><?=$row['subject']?></b></a></div>
          <div class="col-md-5"><b><?=$row['date_mail']?></b></div>
          <div class="col-md-5"><b><?=$row['from_mail']?></b></div>
          <div class="col-md-1"><b><?=($row['dmarc']==1)?'DMARC':''?></b></div>
          <div class="col-md-1"><a href="/delete?id=<?=$row['id']?>" class="btn btn-danger btn-sm">X</a></div>
          
          <?php if(count($data)==1) { ?>
            <div class="col-md-12">
              <p><?=$this->decodeMess($row['message'])?></p>
            </div>
          <?php } ?>
          
        </div>
      <?php } ?>
      
    <?php } else { ?>
    <p>Нет писем</p>
    <a href="?param=getmails">Получить письма</a>
    <?php } ?>
  </div>
</div>
