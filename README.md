# task


Docker Desktop uygulaması açın.

Aşıdaki satırları sırayla komut satırında çalıştırın.

    git clone https://github.com/byldrm/path_task.git
    
    cd path_task
    
    docker compose up -d --build
    
    docker exec -it app-shared-php8 bash
    
    cd abc_company
    
    composer update

    php bin/console doctrine:migrations:migrate
    
    php bin/console doctrine:fixtures:load


Postman Collections
https://www.getpostman.com/collections/7e7acee0867ca44789b1
    
Api kullanıcıları

username : user1

password : 12user34

username : user2

password : 12user34

username : user3

password : 12user34

Proje Adımları

    1- Postman Collections linkindeki Login url inden login olup JWT token alınız
    
    2- Oluşan tokenı diğer linklerdeki Headers sekmesinden Authorization satırına  
    
    Bearer  buraya_tokenı_yapıştırın
    
    3- Ürün Listesi urlinden tüm ürünlerin listesini görebilirsiniz
    
    4- Ürün Detay urlinden ürün id si göndererek ürünün detaylarını görebilirsiniz
    
    5- Sepete Ürün eklemek için Sepete Ürün Ekleme linkinden POST methoduyla
        {
        "productId":71,
        "quantity":1
        }
        gönderilen ürün id ve miktar ürün listesinde varsa ve miktar yeterliyse  sepetinize ürünü ekleyiniz
    
    6- Sepet Ürün Listesi urlinden sepetinizdeki ürünlerin listesini görebilirisiniz
    
    7- Sipariş Listesi urlinden tüm siparişlerinizin listesini görebilirsiniz
    
    8- Sepeti Sipariş ver urlinden sepetinizdeki ürünlerle sipariş oluşturabilirsiniz
        sepet sipariş verilince boşalır ve sipariş verilen ürünlerin ürün listesinden miktarları verilen sipariş adedi kadar azalır
    
    9- Sipariş detayı urlinden sipariş id sini girerek sipariş detaylarını görebilirsiniz
    
    10- Sipariş Listesine Yeni Ürün Ekle urlinden Ürünün shippingDate tarihi gelmediyse ve ürün listesinde yeterli adet varsa yeni ürün ekleyebilirsiniz
        eklenen ürünlerin ürün listesinden miktarları verilen sipariş adedi kadar azalır
    
    11- Sipariş Listesiniden Ürün Sil urlinden   Ürünün shippingDate tarihi gelmediyse ürünü silebilirisiniz
        silinen ürünlerin ürün listesinde miktarları silinen ürün kadar artar.
    
    12- Sipariş Adresi Değiştir linkinden  Ürünün shippingDate tarihi gelmediyse sipariş adresini değiştirebilirisiniz
