DROP DATABASE IF EXISTS `cs6400_fa21`; 
SET default_storage_engine=InnoDB;
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS cs6400_fa21 
    DEFAULT CHARACTER SET utf8mb4 
    DEFAULT COLLATE utf8mb4_unicode_ci;
USE cs6400_fa21;

-- Tables 

CREATE TABLE LoggedInUser (
  username varchar(250) NOT NULL,
  password varchar(60) NOT NULL,
  first_name varchar(100) NOT NULL,
  last_name varchar(100) NOT NULL,
  PRIMARY KEY (username)
);


CREATE TABLE SalePeople (
  username varchar(250) NOT NULL,
  PRIMARY KEY (username)
);

CREATE TABLE Owner (
  username varchar(250) NOT NULL,
  PRIMARY KEY (username)
);

CREATE TABLE Manager (
  username varchar(250) NOT NULL,
  PRIMARY KEY (username)
);

CREATE TABLE InventoryClerk (
  username varchar(250) NOT NULL,
  PRIMARY KEY (username)
);

CREATE TABLE ServiceWriter (
  username varchar(250) NOT NULL,
  PRIMARY KEY (username)
);

CREATE TABLE Vehicle (
  vin varchar(50) NOT NULL,
  model_name varchar(250) NULL,
  model_year year NOT NULL,
  invoice_price float(10,2) NOT NULL,
  description varchar(250) NULL,
  manufacturer_name varchar(250) NOT NULL,
  username varchar(250) NOT NULL,
  inventory_added_date  date NOT NULL,
  PRIMARY KEY (vin)
);

CREATE TABLE Manufacturer (
  manufacturer_name varchar(250) NOT NULL,
  PRIMARY KEY (manufacturer_name)
);

CREATE TABLE Car (
  vin varchar(50) NOT NULL,
  number_of_doors int(4) unsigned NOT NULL,
  PRIMARY KEY (vin)
);

CREATE TABLE Convertible (
  vin varchar(50) NOT NULL,
  back_seat_count int(4) unsigned NOT NULL,
  roof_type  varchar(25) NOT NULL,
  PRIMARY KEY (vin)
);

CREATE TABLE Truck (
  vin varchar(50) NOT NULL,
  cargo_cover_type varchar(25) NULL,
  cargo_capacity int(4) unsigned NOT NULL,
  number_of_rear_axies int(4) unsigned NOT NULL,
  PRIMARY KEY (vin)
);

CREATE TABLE Van (
  vin varchar(50) NOT NULL,
  has_drive_side_door int(1) NOT NULL,
  PRIMARY KEY (vin)
);

CREATE TABLE Suv (
  vin varchar(50) NOT NULL,
  drivetrain_type varchar(25) NOT NULL,
  num_of_cupholders int(4) unsigned NOT NULL,
  PRIMARY KEY (vin)
);

CREATE TABLE VehicleColor (
  vin varchar(50) NOT NULL,
  color varchar(250) NOT NULL,
  PRIMARY KEY (vin, color)
  );
 
 CREATE TABLE Sale (
  vin varchar(50) NOT NULL,
  customerID int(16) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(250) NOT NULL,
  sale_date date NOT NULL,
  sale_price float(10,2) NOT NULL,
  PRIMARY KEY (customerID, username,vin)
);


  
CREATE TABLE Repair(
	vin varchar(50) NOT NULL,
	start_date date NOT NULL,
    	labor_charge float(10,2) NOT NULL,
	description varchar(250) NOT NUll,
	complete_date date NULL,
  	odometer_reading int NOT NULL,
	customerID int(16) unsigned NOT NULL,
    	username varchar(50) NOT NULL,
	PRIMARY KEY (vin,start_date)
	);

CREATE TABLE Parts(
	vin varchar(50) NOT NULL,
	start_date date NOT NULL,
	vendor_name varchar(50) NOT NULL,
	part_number varchar(50) NOT NULL,
	quantity int NOT NULL,
	price float(10,2) NOT NULL,
	PRIMARY KEY (vin, start_date,vendor_name, part_number)
		);
			 
CREATE TABLE Individual (
  driver_license varchar(60) NOT NULL,
  first_name varchar(250) NOT NULL,
  last_name varchar(250) NOT NULL,
  customerID int(16) unsigned NOT NULL,
  PRIMARY KEY (driver_license)

);

CREATE TABLE Business(
  tax_id_number varchar(60) NOT NULL,
  business_name varchar(250) NOT NULL,
  primary_contact_first_name varchar(250) NOT NULL,
  primary_contact_last_name varchar(250) NOT NULL,
  primary_contact_title varchar(100) NOT NULL,
  customerID int(16) unsigned NOT NULL,
  PRIMARY KEY (tax_id_number)

);

CREATE TABLE Customer (
  customerID int(16) unsigned NOT NULL AUTO_INCREMENT,
  phone_number varchar(100) NOT NULL,
  email_address varchar(250) NULL,
  street_address varchar(500) NOT NULL,
  city varchar(500) NOT NULL,
  state varchar(500) NOT NULL,
  postal_code varchar(100) NOT NULL,
  PRIMARY KEY (customerID)
);

  
 ALTER TABLE SalePeople
  ADD CONSTRAINT fk_SalePeople_username_LoggedInUser_username FOREIGN KEY (username) REFERENCES LoggedInUser (username);
  
ALTER TABLE Owner
  ADD CONSTRAINT fk_Owner_username_LoggedInUser_username FOREIGN KEY (username) REFERENCES LoggedInUser (username);
  
ALTER TABLE Manager
  ADD CONSTRAINT fk_Manager_username_LoggedInUser_username FOREIGN KEY (username) REFERENCES LoggedInUser (username);
  
ALTER TABLE InventoryClerk
  ADD CONSTRAINT fk_InventoryClerk_username_LoggedInUser_username FOREIGN KEY (username) REFERENCES LoggedInUser (username);
  
ALTER TABLE ServiceWriter
  ADD CONSTRAINT fk_ServiceWriter_username_LoggedInUser_username FOREIGN KEY (username) REFERENCES LoggedInUser (username);  

ALTER TABLE Repair
  ADD CONSTRAINT fk_Repair_vin_Vehicle_vin FOREIGN KEY (vin) REFERENCES Vehicle (vin);
  
ALTER TABLE Repair
  ADD CONSTRAINT fk_Repair_customerID_Customer_customerID FOREIGN KEY (customerID) REFERENCES Customer (customerID);
  
ALTER TABLE Repair
  ADD CONSTRAINT fk_Repair_username_LoggedInUser_username FOREIGN KEY (username) REFERENCES LoggedInUser (username);
	
ALTER TABLE Parts
  ADD CONSTRAINT fk_Parts_vin_Parts_start_date_Repair_vin_Repair_start_date FOREIGN KEY (vin, start_date) REFERENCES Repair (vin,start_date);
  
ALTER TABLE Vehicle
  ADD CONSTRAINT fk_Vehicle_username_LoggedInUser_username FOREIGN KEY (username) REFERENCES LoggedInUser (username);

ALTER TABLE Vehicle
  ADD CONSTRAINT fk_Vehicle_manufacturer_name_manufacturer_manufacturer_name FOREIGN KEY (manufacturer_name) REFERENCES Manufacturer (manufacturer_name);

ALTER TABLE Car
  ADD CONSTRAINT fk_Car_vin_Vehicle_vin FOREIGN KEY (vin) REFERENCES Vehicle (vin);

ALTER TABLE Convertible
  ADD CONSTRAINT fk_Convertible_vin_Vehicle_vin FOREIGN KEY (vin) REFERENCES Vehicle (vin);

ALTER TABLE Truck
  ADD CONSTRAINT fk_Truck_vin_Vehicle_vin FOREIGN KEY (vin) REFERENCES Vehicle (vin);

ALTER TABLE Van
  ADD CONSTRAINT fk_Van_vin_Vehicle_vin FOREIGN KEY (vin) REFERENCES Vehicle (vin);

ALTER TABLE Suv
  ADD CONSTRAINT fk_Suv_vin_Vehicle_vin FOREIGN KEY (vin) REFERENCES Vehicle (vin);

ALTER TABLE VehicleColor
  ADD CONSTRAINT fk_VehicleColor_vin_Vehicle_vin FOREIGN KEY (vin) REFERENCES Vehicle (vin);
 
ALTER TABLE Sale
  ADD CONSTRAINT fk_Sale_vin_Vehicle_vin FOREIGN KEY (vin) REFERENCES Vehicle (vin);

ALTER TABLE Sale
  ADD CONSTRAINT fk_Sale_username_LoggedInUser_username FOREIGN KEY (username) REFERENCES LoggedInUser (username);
  
 ALTER TABLE Sale
  ADD CONSTRAINT fk_Sale_customerID_Customer_customerID FOREIGN KEY (customerID) REFERENCES Customer (customerID);
  
ALTER TABLE Business
  ADD CONSTRAINT fk_Business_customerID_Customer_customerID FOREIGN KEY (customerID) REFERENCES Customer (customerID);

ALTER TABLE Individual
  ADD CONSTRAINT fk_Individual_customerID_Customer_customerID FOREIGN KEY (customerID) REFERENCES Customer (customerID);
