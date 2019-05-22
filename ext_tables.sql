#
# Table structure for table 'tx_cartproducts_domain_model_product_product'
#
CREATE TABLE tx_cartproducts_domain_model_product_product (
	is_subscription smallint(6) unsigned DEFAULT '0' NOT NULL,
	paypal_type varchar(255) DEFAULT '' NOT NULL,
	paypal_category varchar(255) DEFAULT '' NOT NULL,
	paypal_product_id varchar(255) DEFAULT '' NOT NULL,
	paypal_plan_id varchar(255) DEFAULT '' NOT NULL,
);
