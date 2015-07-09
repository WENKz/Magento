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
    public function getProduit($id_boutique, $name_attribute,$cat = null,$name_attribute2,$name_attribute3) {
      
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );
			$products = Mage::getModel('catalog/product')->getCollection()->setStoreId(STORE_ID)
                ->addAttributeToSelect("name")
                ->addAttributeToSelect("sku")
                ->addAttributeToSelect($name_attribute)
				->addAttributeToSelect($name_attribute2)
				->addAttributeToSelect($name_attribute3)
                ->addAttributeToFilter('status', array('eq' => 1))
                ->addAttributeToFilter('visibility', $visibility);
     if($cat != null || $cat != ""){
			
			
				

		$products->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                ->addAttributeToFilter('category_id', array('eq'=> $cat));

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
	 <select name="attribute3">
        <?php
        foreach ($check->getAttributes() as $attribute) {
            ?>
            <option value="<?php echo $attribute->getAttributeCode() ?>" <?php
            if (isset($_GET['attribute3']) && $_GET['attribute3'] == $attribute->getAttributeCode()) {
                echo "selected";
            }
            ?>><?php echo $attribute->getFrontendLabel() ?></option>
                    <?php
                }
                ?>
    </select>
	 <select name="attribute2">
        <?php
        foreach ($check->getAttributes() as $attribute) {
            ?>
            <option value="<?php echo $attribute->getAttributeCode() ?>" <?php
            if (isset($_GET['attribute2']) && $_GET['attribute2'] == $attribute->getAttributeCode()) {
                echo "selected";
            }
            ?>><?php echo $attribute->getFrontendLabel() ?></option>
                    <?php
                }
                ?>
    </select>
	
	<input type="submit"  value="envoyer">
	  <select name="categories">
	  <option></option>
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
<style> 
<!--
table {
  border-width: 1px;
  border-style: solid;
  border-color: black;
  border-collapse: collapse;
  /* width: 50%; */
}
td {
  border-width: 1px;
  border-style: solid;
  border-color: black;
}
-->
</style>
<table>
    <?php
	//var_dump($check->getCategories());
    if (isset($_GET["boutique"]) && isset($_GET["attribute"]) ) {
        $produits = $check->getProduit($_GET["boutique"], strtr(strtolower($_GET["attribute"]), array(" " => "_")),$_GET['categories'],$_GET["attribute2"],$_GET["attribute3"]);
        $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
        foreach ($produits as $product) {
            $attributeSetModel->load($product->getAttributeSetId());
            $label_text = "";
			$label_text2 = "";
			$label_text3 = "";
            $productModel = Mage::getModel('catalog/product');
            $attr = $productModel->getResource()->getAttribute($_GET["attribute"]);
            if ($attr->usesSource()) {
                $label_text = $attr->getSource()->getOptionText($product->$_GET["attribute"]);
            }
            if (!$label_text) {
                $label_text = Mage::helper('core')->escapeHtml($product->$_GET["attribute"]);
            }
			 $attr2 = $productModel->getResource()->getAttribute($_GET["attribute2"]);
            if ($attr2->usesSource()) {
                $label_text2 = $attr2->getSource()->getOptionText($product->$_GET["attribute2"]);
            }
            if (!$label_text2) {
                $label_text2 = Mage::helper('core')->escapeHtml($product->$_GET["attribute2"]);
            }
			
			$attr3 = $productModel->getResource()->getAttribute($_GET["attribute3"]);
            if ($attr3->usesSource()) {
                $label_text3 = $attr3->getSource()->getOptionText($product->$_GET["attribute3"]);
            }
            if (!$label_text3) {
                $label_text3 = Mage::helper('core')->escapeHtml($product->$_GET["attribute3"]);
            }
			
            echo "<tr><td>" . $product->getSku() . "</td><td>" . $product->getName() . "</td><td>" . $product->getVisibility() . "</td><td>" . $product->getStatus() . "</td><td>" . $attributeSetModel->getAttributeSetName() . "</td><td>" ;
			if(is_array ($label_text)){
				foreach($label_text as $lab){
					echo $lab.",";
				}
				echo "</td><td>";
			}else{ 
				echo $label_text . "</td><td>";
			}if(is_array ($label_text3)){
				foreach($label_text3 as $lab3){
					echo $lab3.",";
				}
				echo "</td><td>";
			}else{ 
				echo $label_text3 . "</td><td>";
			}if(is_array ($label_text2)){
				foreach($label_text2 as $lab2){
					echo $lab2.",";
				}
				echo "</td></tr>";
			}else{ 
				echo $label_text2 . "</td></tr>";
			}
        }
    }
    ?>
</table>
