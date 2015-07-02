<?php
error_reporting(E_ALL | E_STRICT);
define('MAGENTO_ROOT', getcwd());
$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
 define('STORE_ID', $_GET["boutique"]);Mage::app()->setCurrentStore(STORE_ID);
Mage::app();

class check {

    public function getAttributes() {
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                ->getItems();

        return $attributes;
    }

    public function getProduit($id_boutique, $name_attribute,$cat = null) {
      
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );
     
				if($cat != null){
				$products = Mage::getModel('catalog/product')->getCollection()->setStoreId(STORE_ID)

                ->addAttributeToSelect("name")
                ->addAttributeToSelect("sku")
				
                ->addAttributeToSelect($name_attribute)
									->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')

                ->addAttributeToFilter('status', array('eq' => 1))
                ->addAttributeToFilter('category_id', array('in'=> $cat))
                ->addAttributeToFilter('visibility', $visibility)->load();
				
				}else{
				   $products = Mage::getModel('catalog/product')->getCollection()->setStoreId(STORE_ID)
                ->addAttributeToSelect("name")
                ->addAttributeToSelect("sku")
				
                ->addAttributeToSelect($name_attribute)
                ->addAttributeToFilter('status', array('eq' => 1))
                ->addAttributeToFilter('visibility', $visibility)->load();
				}

        return $products;
    }
	public function getCategories(){
	$categories = Mage::getModel('catalog/category')->getCollection()  
    ->addAttributeToSelect('name')
	->addAttributeToSelect('entity_id')
    ->load();
	
	return $categories;
	}
}

$check = new check();
?>
<form id="form">
    <select name="boutique">
        <option value="1" <?php
        if (isset($_GET['boutique']) && $_GET['boutique']  == 1) {
            echo "selected";
        }
        ?>>KingVapo</option>
        <option value="2" <?php
        if (isset($_GET['boutique']) && $_GET['boutique'] == 2) {
            echo "selected";
        }
        ?>>KingVapo AntiBug</option>
        <option value="3" <?php
        if (isset($_GET['boutique']) && $_GET['boutique']  == 3) {
            echo "selected";
        }
        ?>>Sfactory</option>
        <option value="0" <?php
        if (isset($_GET['boutique']) && $_GET['boutique']  == 0) {
            echo "selected";
        }
        ?>>Admin</option>
    </select>
  
	  <select name="attribute">
        <?php
        foreach ($check->getAttributes() as $attribute) {
            ?>
            <option value="<?php echo $attribute->getAttributeCode() ?>" <?php
            if (isset($_GET['attribute']) && $_GET['attribute'] == $attribute->getAttributeCode()) {
                echo "selected";
            }
            ?>><?php echo $attribute->getFrontendLabel() ?></option>
                    <?php
                }
                ?>
    </select>
	<input type="submit"  value="envoyer">
	  <select name="categories">
        <?php
        foreach ($check->getCategories() as $categorie) {
			?>
			<option value="<?php echo $categorie["entity_id"]?>" <?php if (isset($_GET['categories']) && $_GET['categories'] == $categorie["entity_id"]) {
                echo "selected";
            }?>><?php echo $categorie["name"] ?></option>
			<?php
                }
                ?>
    </select>
    
</form>
<table>
    <?php
	//var_dump($check->getCategories());
    if (isset($_GET["boutique"]) && isset($_GET["attribute"]) ) {
        $produits = $check->getProduit($_GET["boutique"], strtr(strtolower($_GET["attribute"]), array(" " => "_")),$_GET['categories']);
        $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
        foreach ($produits as $product) {

            $attributeSetModel->load($product->getAttributeSetId());
            $label_text = "";
            $productModel = Mage::getModel('catalog/product');
            $attr = $productModel->getResource()->getAttribute($_GET["attribute"]);
            if ($attr->usesSource()) {
                $label_text = $attr->getSource()->getOptionText($product->$_GET["attribute"]);
            }
            if (!$label_text) {
                $label_text = Mage::helper('core')->escapeHtml($product->$_GET["attribute"]);
            }
			
            echo "<tr><td>" . $product->getSku() . "</td><td>" . $product->getName() . "</td><td>" . $product->getVisibility() . "</td><td>" . $product->getStatus() . "</td><td>" . $attributeSetModel->getAttributeSetName() . "</td><td>" . $label_text . "</td></tr>";
        }
    }
    ?>
</table>
