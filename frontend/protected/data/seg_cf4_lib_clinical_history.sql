CREATE TABLE `gmmci041518`.`seg_cf4_lib_clinical_history`(
  `id` SMALLINT(5) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200),
  `is_active` TINYINT(1),
  `ordering` SMALLINT(5),
  `risk_factor` VARCHAR(10),
  PRIMARY KEY (`id`)
);

INSERT INTO `seg_cf4_lib_clinical_history` (
  `id`,
  `name`,
  `is_active`,
  `ordering`,
  `risk_factor`
)
VALUES
  (
    1,
    'Multiple pregnancy',
    1,
    1,
    'obstetric'
  ),
  (2, 'Ovarian cyst', 1, 2, 'obstetric'),
  (3, 'Myoma uteri', 1, 3, 'obstetric'),
  (
    4,
    'Placenta previa',
    1,
    4,
    'obstetric'
  ),
  (
    5,
    'History of 3 miscarriages',
    1,
    5,
    'obstetric'
  ),
  (
    6,
    'History of stillbirth',
    1,
    6,
    'obstetric'
  ),
  (
    7,
    'History of pre-eclampsia',
    1,
    7,
    'obstetric'
  ),
  (
    8,
    'Premature contraction',
    1,
    9,
    'obstetric'
  ),
  (9, 'Hypertension', 1, 1, 'medical'),
  (10, 'Heart disease', 1, 2, 'medical'),
  (11, 'Diabetes', 1, 3, 'medical'),
  (
    12,
    'Thyroid disorder',
    1,
    4,
    'medical'
  ),
  (13, 'Obesity', 1, 5, 'medical'),
  (
    14,
    'Moderate to severe asthma',
    1,
    6,
    'medical'
  ),
  (15, 'Epilepsy', 1, 7, 'medical'),
  (16, 'Renal Disease', 1, 8, 'medical'),
  (
    17,
    'Bleeding disorders',
    1,
    9,
    'medical'
  ),
  (
    18,
    'History of previous cesarian section',
    1,
    10,
    'medical'
  ),
  (
    19,
    'History of uterine myomectomy',
    1,
    11,
    'medical'
  ),
  (
    20,
    'History of eclampsia',
    1,
    8,
    'obstetric'
  ) ;

