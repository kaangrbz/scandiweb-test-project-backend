abstract class Product {
  protected $sku;
  protected $name;
  protected $price;

  public function __construct($sku, $name, $price) {
    $this->sku = $sku;
    $this->name = $name;
    $this->price = $price;
  }

  public function getSku() {
    return $this->sku;
  }

  public function getName() {
    return $this->name;
  }

  public function getPrice() {
    return $this->price;
  }

  // abstract method that must be implemented by subclasses
  abstract public function getSpecificAttribute();
}

class DVD extends Product {
  protected $size;

  public function __construct($sku, $name, $price, $size) {
    parent::__construct($sku, $name, $price);
    $this->size = $size;
  }

  public function getSpecificAttribute() {
    return $this->size . " MB";
  }
}

class Book extends Product {
  protected $weight;

  public function __construct($sku, $name, $price, $weight) {
    parent::__construct($sku, $name, $price);
    $this->weight = $weight;
  }

  public function getSpecificAttribute() {
    return $this->weight . " Kg";
  }
}

class Furniture extends Product {
  protected $height;
  protected $width;
  protected $length;

  public function __construct($sku, $name, $price, $height, $width, $length) {
    parent::__construct($sku, $name, $price);
    $this->height = $height;
    $this->width = $width;
    $this->length = $length;
  }

  public function getSpecificAttribute() {
    return $this->height . "x" . $this->width . "x" . $this->length . " cm";
  }
}
