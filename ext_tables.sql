#
# Table structure for table 'tx_cart_domain_model_order_item'
#
CREATE TABLE tx_cart_domain_model_order_item (
	paypal_subscription_id varchar(255) DEFAULT '' NOT NULL,
);


#
# Table structure for table 'tx_cartproducts_domain_model_product_product'
#
CREATE TABLE tx_cartproducts_domain_model_product_product (
	is_subscription smallint(6) unsigned DEFAULT '0' NOT NULL,
	paypal_type varchar(255) DEFAULT '' NOT NULL,
	paypal_category varchar(255) DEFAULT '' NOT NULL,
	paypal_product_id varchar(255) DEFAULT '' NOT NULL,
	paypal_plan_id varchar(255) DEFAULT '' NOT NULL,
	paypal_setup_failure varchar(30) DEFAULT '' NOT NULL,
	paypal_failure_threshold int(11) unsigned DEFAULT '0',
	paypal_sequence int(11) unsigned DEFAULT '0',
);


#
# Table structure for table 'tx_paypalsubscription_domain_model_sequence'
#
CREATE TABLE tx_paypalsubscription_domain_model_sequence (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	product int(11) unsigned DEFAULT '0',

	type varchar(30) DEFAULT '' NOT NULL,
	interval_unit varchar(30) DEFAULT '' NOT NULL,
	interval_count int(11) unsigned NOT NULL default '0',
	total_cycles int(11) unsigned NOT NULL default '0',
	price double(11,2) DEFAULT '0.00' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(6) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(6) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);
