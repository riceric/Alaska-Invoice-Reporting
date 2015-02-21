DROP TABLE IF EXISTS orderinfo_has_vcode;

DROP TABLE IF EXISTS orderinfo;

DROP TABLE IF EXISTS customer;

DROP TABLE IF EXISTS vcode;

CREATE TABLE vcode (
  code VARCHAR(5)  NOT NULL  ,
  cost DECIMAL(10,2)  NOT NULL  ,
  description VARCHAR(255)  NOT NULL    ,
PRIMARY KEY(code));



CREATE TABLE customer (
  customer_num INTEGER UNSIGNED  NOT NULL   AUTO_INCREMENT,
  comp_name VARCHAR(255)  NULL    ,
PRIMARY KEY(customer_num));



CREATE TABLE orderinfo (
  job_id INTEGER UNSIGNED  NOT NULL  ,
  customer_num INTEGER UNSIGNED  NOT NULL  ,
  service_date DATE  NOT NULL  ,
  amount DECIMAL(10,2)  NOT NULL  ,
  order_status INTEGER UNSIGNED  NOT NULL DEFAULT 0 ,
  invoice_num INTEGER UNSIGNED  NULL  ,
  recipient_id INTEGER UNSIGNED  NOT NULL  ,
  patient_fname VARCHAR(45)  NOT NULL  ,
  patient_lname VARCHAR(45)  NOT NULL  ,
  patient_dob DATE  NOT NULL  ,
  patient_gender CHAR  NOT NULL  ,
  prior_auth_num INTEGER UNSIGNED  NULL  ,
  diagnosis_code VARCHAR(20)  NULL  ,
  od_sph DECIMAL(10,2)  NULL  ,
  od_cyl DECIMAL(10,2)  NULL  ,
  od_multi VARCHAR(20)  NULL  ,
  od_psm VARCHAR(20)  NULL  ,
  os_sph DECIMAL(10,2)  NULL  ,
  os_cyl DECIMAL(10,2)  NULL  ,
  os_multi VARCHAR(20)  NULL  ,
  os_psm VARCHAR(20)  NULL  ,
  frame BOOL  NULL  ,
  slab_off BOOL  NULL    ,
PRIMARY KEY(job_id)  ,
INDEX orderinfo_FKIndex1(customer_num),
  FOREIGN KEY(customer_num)
    REFERENCES customer(customer_num)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION);



CREATE TABLE orderinfo_has_vcode (
  orderinfo_job_id INTEGER UNSIGNED  NOT NULL  ,
  vcode_code VARCHAR(5)  NOT NULL  ,
  count INTEGER UNSIGNED  NOT NULL    ,
PRIMARY KEY(orderinfo_job_id, vcode_code)  ,
INDEX orderinfo_has_vcode_FKIndex1(orderinfo_job_id)  ,
INDEX orderinfo_has_vcode_FKIndex2(vcode_code),
  FOREIGN KEY(orderinfo_job_id)
    REFERENCES orderinfo(job_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(vcode_code)
    REFERENCES vcode(code)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION);
