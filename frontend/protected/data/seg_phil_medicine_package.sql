CREATE TABLE `gmmci041518`.`seg_phil_medicine_package`(
  `package_code` VARCHAR(20) NOT NULL DEFAULT '',
  `package_desc` TEXT,
  `date_added` DATE,
  PRIMARY KEY (`package_code`)
);

INSERT INTO `gmmci041518`.`seg_phil_medicine_package` (
  `package_code`,
  `package_desc`,
  `date_added`
)
VALUES
  ('100MB', '100 ML BOTTLE', NULL),
  ('100ML', '100 ML VIAL', NULL),
  ('100PC', '100S', NULL),
  ('105ML', '105 ML BOTTLE', NULL),
  ('10MLA', '10 ML AMPUL', NULL),
  ('10MLB', '10 ML BOTTLE', NULL),
  (
    '10MLR',
    '10 ML RESP SOLUTION',
    NULL
  ),
  ('10MLV', '10 ML VIAL', NULL),
  ('10MLX', '10 ML', NULL),
  ('10PCS', '10S', NULL),
  ('11PCS', '11S', NULL),
  ('120MB', '120 ML BOTTLE', NULL),
  ('120ML', '120 ML', NULL),
  ('125GA', '1.25 G SACHET', NULL),
  ('12PCS', '12S', NULL),
  ('14PCS', '14S', NULL),
  ('150ML', '150 ML', NULL),
  ('15MLB', '15 ML BOTTLE', NULL),
  (
    '15MLR',
    '15 ML RESP SOLUTION',
    NULL
  ),
  ('15MLV', '15 ML VIAL', NULL),
  ('15MLX', '15 ML', NULL),
  ('1MLAM', '1 ML AMPUL', NULL),
  ('1MLBT', '1 ML BOTTLE', NULL),
  ('1MLVI', '1 ML VIAL', NULL),
  ('1THPC', '1000S', NULL),
  ('200PC', '200S', NULL),
  ('20MGL', '20 MG/ML AMPULE', NULL),
  (
    '20MLR',
    '20 ML RESP SOLUTION',
    NULL
  ),
  ('20MLV', '20 ML VIAL', NULL),
  ('20PCS', '20S', NULL),
  ('21PCS', '21S', NULL),
  ('22MLB', '22 ML BOTTLE', NULL),
  ('250MB', '250 ML BOTTLE', NULL),
  ('250ML', '250 ML', NULL),
  ('25MLA', '2.5 ML AMPULE', NULL),
  ('25MLB', '2.5 ML BOTTLE', NULL),
  ('25MLN', '2.5 ML NEBULE', NULL),
  (
    '25MLR',
    '2.5 ML RESP. SOLUTION',
    NULL
  ),
  ('25MLV', '25 ML VIAL', NULL),
  ('25PCS', '25S', NULL),
  ('2MGBT', '2 MG BOTTLE', NULL),
  ('2MLAM', '2 ML AMPUL', NULL),
  ('2MLAV', '2 ML AMPUL/VIAL', NULL),
  ('2MLBT', '2 ML BOTTLE', NULL),
  ('2MLNE', '2 ML NEBULE', NULL),
  (
    '2MLRS',
    '2 ML RESP SOLUTION',
    NULL
  ),
  ('2MLVI', '2 ML VIAL', NULL),
  ('2P5ML', '2.5 ML VIAL', NULL),
  ('30MLB', '30 ML BOTTLE', NULL),
  ('30MLX', '30 ML', NULL),
  ('30PCS', '30S', NULL),
  ('35MLB', '35 ML BOTTLE', NULL),
  ('39MLB', '39 ML BOTTLE', NULL),
  ('3MLAM', '3 ML AMPUL', NULL),
  ('4FLAP', 'BOX OF 4 FLAPS', NULL),
  ('4MLAG', '4 ML BOTTLE', NULL),
  ('4MLAM', '4 ML AMPUL', NULL),
  ('4MLVI', '4 ML VIAL', NULL),
  ('500PC', '500S', NULL),
  ('50MLB', '50 ML BOTTLE', NULL),
  ('50MLV', '50 ML VIAL', NULL),
  ('50PCS', '50S', NULL),
  ('5MLAM', '5 ML AMPUL', NULL),
  ('5MLBT', '5 ML BOTTLE', NULL),
  ('5MLVI', '5 ML VIAL', NULL),
  ('5PCSX', '5S', NULL),
  ('60MLB', '60 ML BOTTLE', NULL),
  ('60MLS', '60 ML SUSPENSION', NULL),
  ('60MLV', '60 ML VIAL', NULL),
  ('60MLX', '60 ML', NULL),
  ('60PCS', '60S', NULL),
  ('6MLVX', '6 ML VIAL', NULL),
  ('6PCSB', '6S', NULL),
  ('70MLB', '70 ML BOTTLE', NULL),
  ('75MLB', '75 ML BOTTLE', NULL),
  ('75MLV', '7.5 ML VIAL', NULL),
  ('7MLVI', '7 ML VIAL', NULL),
  ('8PCSX', '8S', NULL),
  ('9PCSX', '9S', NULL),
  (
    'AM100',
    'AMBER BOTTLE OF 100S',
    NULL
  ),
  (
    'AM25S',
    'AMBER BOTTLE OF 25S',
    NULL
  ),
  (
    'AM500',
    'AMBER BOTTLE OF 500S',
    NULL
  ),
  (
    'AM50S',
    'AMBER BOTTLE OF 50S',
    NULL
  ),
  ('AMBGL', 'AMBER GLASS', NULL),
  ('AMBOT', 'AMBER BOTTLE', NULL),
  ('AMBVI', 'AMBER VIAL', NULL),
  ('AMPUL', 'AMPULE', NULL),
  (
    'B1010',
    'BLISTER PACK OF 10S (BOX OF 10S)',
    NULL
  ),
  (
    'B1040',
    'BLISTER PACK OF 10S (BOX OF 40S)',
    NULL
  ),
  (
    'B1050',
    'BLISTER PACK OF 10S (BOX OF 50S)',
    NULL
  ),
  (
    'B1060',
    'BLISTER PACK BY 10S (BOX OF 60S)',
    NULL
  ),
  (
    'B1080',
    'BLISTER PACK OF 10S (BOX OF 80S)',
    NULL
  ),
  (
    'B10SH',
    'BLISTER PACK OF 10S (BOX OF 100S)',
    NULL
  ),
  (
    'B1412',
    'BLISTER PACK BY 14S (BOX OF 112S)',
    NULL
  ),
  (
    'B1414',
    'BLISTER PACK OF 14S (BOX OF 140S)',
    NULL
  ),
  (
    'B1428',
    'BLISTER PACK OF 14S (BOX OF 28S)',
    NULL
  ),
  (
    'B1429',
    'BLISTER PACK OF 14S (BOX OF 294S)',
    NULL
  ),
  (
    'B1515',
    'BLISTER PACK OF 15S (BOX OF 150S)',
    NULL
  ),
  (
    'B1530',
    'BLISTER PACK OF 15S (BOX OF 30S)',
    NULL
  ),
  (
    'B2040',
    'BLISTER PACK OF 20S (BOX OF 40S)',
    NULL
  ),
  (
    'B2080',
    'BLISTER PACK OF 20S (BOX OF 80S)',
    NULL
  ),
  (
    'B3060',
    'BLISTER PACK OF 30S (BOX OF 60S)',
    NULL
  ),
  (
    'B550S',
    'BLISTER PACK BY 5S (BOX OF 100S)',
    NULL
  ),
  (
    'B8120',
    'BLISTER PACK OF 8S (BOX OF 120S)',
    NULL
  ),
  ('BAGXX', 'BAG', NULL),
  (
    'BF100',
    'BLISTER FOIL BY 100S',
    NULL
  ),
  (
    'BF250',
    'BLISTER FOIL BY 250S',
    NULL
  ),
  (
    'BF30X',
    'BLISTER FOIL BY 30S',
    NULL
  ),
  (
    'BF60S',
    'BLISTER FOIL BY 60S',
    NULL
  ),
  ('BFOIL', 'BLISTER FOIL', NULL),
  (
    'BFT10',
    'BLISTER FOIL BY 10S',
    NULL
  ),
  (
    'BFTCL',
    'BLISTER FOIL BY 10S (BOX OF 50S)',
    NULL
  ),
  (
    'BFTCX',
    'BLISTER FOIL BY 10S (BOX OF 100S)',
    NULL
  ),
  (
    'BFTLX',
    'BLISTER FOIL BY 50S',
    NULL
  ),
  ('BOTTL', 'BOTTLE', NULL),
  (
    'BOX10',
    'BLISTER BOX OF 10',
    NULL
  ),
  (
    'BOX30',
    'BLISTER BOX OF 30',
    NULL
  ),
  (
    'BOX50',
    'BLISTER BOX OF 50',
    NULL
  ),
  ('BOXHX', 'BOX OF 100S', NULL),
  ('BOXLX', 'BOX OF 50S', NULL),
  ('BOXTX', 'BOX OF 10S', NULL),
  (
    'BP100',
    'BLISTER PACK X 100',
    NULL
  ),
  (
    'BP10X',
    'BLISTER PACK X 10',
    NULL
  ),
  (
    'BP110',
    'BLISTER PACK BY 10S (BOX OF 110S)',
    NULL
  ),
  (
    'BP112',
    'BLISTER PACK X 112',
    NULL
  ),
  (
    'BP141',
    'BLISTER PACK OF 14S (BOX OF 14S)',
    NULL
  ),
  (
    'BP144',
    'BLISTER PACK BY 14S (BOX OF 140S)',
    NULL
  ),
  (
    'BP14X',
    'BLISTER PACK X 14',
    NULL
  ),
  (
    'BP150',
    'BLISTER PACK X 150',
    NULL
  ),
  (
    'BP200',
    'BLISTER PACK X 200',
    NULL
  ),
  (
    'BP202',
    'BLISTER PACK OF 20S (BOX OF 20S)',
    NULL
  ),
  (
    'BP205',
    'BLISTER PACK OF 20S (BOX OF 5S)',
    NULL
  ),
  (
    'BP20H',
    'BLISTER PACK OF 20S (BOX OF 100S)',
    NULL
  ),
  (
    'BP20L',
    'BLISTER PACK OF 20S (BOX OF 50S)',
    NULL
  ),
  (
    'BP20X',
    'BLISTER PACK X 20',
    NULL
  ),
  (
    'BP25H',
    'BLISTER PACK OF 20S (BOX OF 500S)',
    NULL
  ),
  (
    'BP294',
    'BLISTER PACK X 294',
    NULL
  ),
  (
    'BP2T2',
    'BLISTER PACK OF 20S (BOX OF 200S)',
    NULL
  ),
  (
    'BP2TH',
    'BLISTER PACK OF 20S (BOX OF 1000S)',
    NULL
  ),
  (
    'BP30X',
    'BLISTER PACK X 30',
    NULL
  ),
  (
    'BP324',
    'BLISTER PACK OF 3S (BOX OF 24S)',
    NULL
  ),
  (
    'BP330',
    'BLISTER PACK OF 3S (BOX OF 30S)',
    NULL
  ),
  (
    'BP3X3',
    'BLISTER PACK OF 3S (BOX OF 3S)',
    NULL
  ),
  ('BP3XX', 'BLISTER PACK X 3', NULL),
  (
    'BP412',
    'BLISTER PACK OF 4S (BOX OF 12S)',
    NULL
  ),
  (
    'BP420',
    'BLISTER PACK OF 4S (BOX OF 20S)',
    NULL
  ),
  (
    'BP440',
    'BLISTER PACK OF 4S (BOX OF 40S)',
    NULL
  ),
  (
    'BP460',
    'BLISTER PACK OF 4S (BOX OF 60S)',
    NULL
  ),
  (
    'BP48S',
    'BLISTER PACK X 48',
    NULL
  ),
  ('BP4XX', 'BLISTER PACK X 4', NULL),
  (
    'BP50X',
    'BLISTER PACK X 50',
    NULL
  ),
  (
    'BP510',
    'BLISTER PACK OF 5S (BOX OF 10S)',
    NULL
  ),
  (
    'BP515',
    'BLISTER PACK BY 5S (BOX OF 15S)',
    NULL
  ),
  (
    'BP520',
    'BLISTER PACK OF 5S X 20 (BOX OF 100S)',
    NULL
  ),
  (
    'BP530',
    'BLISTER PACK BY 5S (BOX OF 30S)',
    NULL
  ),
  (
    'BP550',
    'BLISTER PACK OF 5S (BOX OF 50S)',
    NULL
  ),
  (
    'BP560',
    'BLISTER PACK BY 5S (BOX OF 60S)',
    NULL
  ),
  (
    'BP5TW',
    'BLISTER PACK BY 5S (BOX OF 20S)',
    NULL
  ),
  (
    'BP5X5',
    'BLISTER PACK OF 5S (BOX OF 5S)',
    NULL
  ),
  ('BP5XX', 'BLISTER PACK X 5', NULL),
  (
    'BP60S',
    'BLISTER PACK OF 20S (BOX OF 60S)',
    NULL
  ),
  (
    'BP60X',
    'BLISTER PACK X 60',
    NULL
  ),
  (
    'BP624',
    'BLISTER PACK OF 6S (BOX OF 24S)',
    NULL
  ),
  (
    'BP630',
    'BLISTER PACK OF 6S (BOX OF 30S)',
    NULL
  ),
  (
    'BP648',
    'BLISTER PACK OF 6S (BOX OF 48S)',
    NULL
  ),
  (
    'BP660',
    'BLISTER PACK BY 6S (BOX OF 60S)',
    NULL
  ),
  ('BP6SX', 'BLISTER PACK X 6', NULL),
  (
    'BP714',
    'BLISTER PACK OF 7S (BOX OF 14S)',
    NULL
  ),
  (
    'BP724',
    'BLISTER PACK OF 7S (BOX OF 24S)',
    NULL
  ),
  (
    'BP728',
    'BLISTER PACK BY 7S (BOX OF 28S)',
    NULL
  ),
  ('BP7SX', 'BLISTER PACK X 7', NULL),
  (
    'BP824',
    'BLISTER PACK OF 8S (BOX OF 24S)',
    NULL
  ),
  (
    'BP832',
    'BLISTER PACK BY 8S (BOX OF 32S)',
    NULL
  ),
  (
    'BP840',
    'BLISTER PACK BY 8S (BOX OF 40S)',
    NULL
  ),
  (
    'BP848',
    'BLISTER PACK OF 8S (BOX OF 48S)',
    NULL
  ),
  (
    'BP860',
    'BLISTER PACK OF 8S (BOX OF 60S)',
    NULL
  ),
  (
    'BP880',
    'BLISTER PACK BY 8S (BOX OF 80S)',
    NULL
  ),
  ('BP8SX', 'BLISTER PACK X 8', NULL),
  (
    'BPC10',
    'BLISTER PACK OF 100S (BOX OF 10S)',
    NULL
  ),
  (
    'BPHXH',
    'BLISTER PACK OF 100S (BOX OF 100S)',
    NULL
  ),
  (
    'BPV20',
    'BLISTER PACK OF 5S (BOX OF 20S)',
    NULL
  ),
  (
    'BPX12',
    'BLISTER PACK OF 10S (BOX OF 120S)',
    NULL
  ),
  (
    'BPX20',
    'BLISTER PACK OF 10S (BOX OF 20S)',
    NULL
  ),
  (
    'BPX2H',
    'BLISTER PACK BY 10S (BOX OF 200S)',
    NULL
  ),
  (
    'BPX30',
    'BLISTER PACK OF 10S (BOX OF 30S)',
    NULL
  ),
  (
    'BPX5H',
    'BLISTER PACK BY 10S (BOX OF 500S)',
    NULL
  ),
  (
    'BPX90',
    'BLISTER PACK BY 10S (BOX OF 90S)',
    NULL
  ),
  ('BPXXX', 'BLISTER PACK', NULL),
  (
    'BS100',
    'BLISTER STRIP X 100',
    NULL
  ),
  (
    'BS10H',
    'BLISTER STRIP BY 10 (BOX OF 100S)',
    NULL
  ),
  (
    'BS10S',
    'BLISTER STRIP BY 10S',
    NULL
  ),
  (
    'BS2HX',
    'BLISTER STRIP 20S (BOX OF 100S)',
    NULL
  ),
  (
    'BS310',
    'CLEAR PVC/ALUMINUM BLISTER STRIP 3 X 10S',
    NULL
  ),
  (
    'BS50X',
    'BLISTER STRIP X 50',
    NULL
  ),
  (
    'BS520',
    'BLISTER STRIP 5 X 20S (BOX OF 100S)',
    NULL
  ),
  (
    'BS610',
    'CLEAR PVC/ALUMINUM BLISTER STRIP 6 X 10S',
    NULL
  ),
  (
    'BS7XX',
    'BLISTER STRIP BY 7',
    NULL
  ),
  (
    'BST20',
    'BLISTER STRIP BY 10 (BOX OF 20S)',
    NULL
  ),
  (
    'BST50',
    'BLISTER STRIP BY 10 (BOX OF 50S)',
    NULL
  ),
  (
    'BSTTH',
    'BLISTER STRIP 10 X 10S (BOX OF 100S)',
    NULL
  ),
  ('BSXXX', 'BLISTER STRIP', NULL),
  (
    'BX150',
    'BLISTER PACK OF 10S (BOX OF 150S)',
    NULL
  ),
  ('CANIS', 'CANISTER', NULL),
  ('COLOA', 'COLORLESS AMPUL', NULL),
  ('COLOG', 'COLORLESS GLASS', NULL),
  (
    'DFPHS',
    'DOUBLE FOIL BLISTERS, 100S',
    NULL
  ),
  (
    'FBSTR',
    'FOIL BLISTER STRIP',
    NULL
  ),
  (
    'FF10X',
    'FLEX FOIL BY 10S (BOX OF 150S)',
    NULL
  ),
  (
    'FF4HX',
    'FLEX FOIL BY 4S (BOX OF 100S)',
    NULL
  ),
  (
    'FFS25',
    'FLEX FOIL STRIP 25 X 4S (BOX OF 100S)',
    NULL
  ),
  ('FFSXX', 'FLEX FOIL STRIP', NULL),
  ('FFT10', 'FLEX FOIL BY 10S', NULL),
  (
    'FFT50',
    'FLEX FOIL BY 10S (BOX OF 50S)',
    NULL
  ),
  (
    'FP330',
    'FOIL PACK BY 3 (BOX OF 30S)',
    NULL
  ),
  (
    'FP4BC',
    'FOIL PACK BY 4 (BOX OF 100S)',
    NULL
  ),
  (
    'FS100',
    'FOIL STRIP BY 100S',
    NULL
  ),
  (
    'FS101',
    'FOIL STRIP BY 10S (BOX OF 10S)',
    NULL
  ),
  (
    'FS102',
    'FOIL STRIP BY 10S (BOX OF 20S)',
    NULL
  ),
  (
    'FS103',
    'FOIL STRIP BY 10S (BOX OF 30S)',
    NULL
  ),
  (
    'FS105',
    'FOIL STRIP BY 10S (BOX OF 50S)',
    NULL
  ),
  (
    'FS106',
    'FOIL STRIP BY 10S (BOX OF 60S)',
    NULL
  ),
  (
    'FS10L',
    'FOIL STRIP 10 X 10S (BOX OF 50S)',
    NULL
  ),
  (
    'FS10S',
    'FOIL STRIP BY 10S (BOX OF 150S)',
    NULL
  ),
  (
    'FS10T',
    'FOIL STRIP 10 X 10S (BOX OF 100S)',
    NULL
  ),
  (
    'FS10X',
    'FOIL STRIP BY 10S (BOX OF 100S)',
    NULL
  ),
  (
    'FS114',
    'FOIL STRIP BY 10S (BOX OF 14S)',
    NULL
  ),
  (
    'FS14X',
    'FOIL STRIP BY 14S',
    NULL
  ),
  (
    'FS153',
    'FOIL STRIP BY 15 (BOX OF 30S)',
    NULL
  ),
  (
    'FS155',
    'FOIL STRIP 10 B81X 5S (BOX OF 50S)',
    NULL
  ),
  (
    'FS156',
    'FOIL STRIP BY 15 (BOX OF 60S)',
    NULL
  ),
  (
    'FS15X',
    'FOIL STRIP BY 15S',
    NULL
  ),
  (
    'FS250',
    'FOIL STRIP BY 10S (BOX OF 250S)',
    NULL
  ),
  (
    'FS25H',
    'FOIL STRIP BY 25S (BOX OF 100S)',
    NULL
  ),
  (
    'FS28X',
    'FOIL STRIP BY 28S',
    NULL
  ),
  (
    'FS30X',
    'FOIL STRIP BY 30S',
    NULL
  ),
  (
    'FS321',
    'FOIL STRIP BY 3 (BOX OF 21S)',
    NULL
  ),
  (
    'FS3X3',
    'FOIL STRIP BY 3 (BOX OF 3S)',
    NULL
  ),
  (
    'FS412',
    'FOIL STRIP BY 4S (BOX OF 12S)',
    NULL
  ),
  (
    'FS414',
    'FOIL STRIP BY 4S (BOX OF 14S)',
    NULL
  ),
  (
    'FS420',
    'FOIL STRIP BY 4S (BOX OF 20S)',
    NULL
  ),
  (
    'FS421',
    'FOIL STRIP BY 4S (BOX OF 21S)',
    NULL
  ),
  (
    'FS424',
    'FOIL STRIP BY 4S (BOX OF 24S)',
    NULL
  ),
  (
    'FS428',
    'FOIL STRIP BY 4S (BOX OF 28S)',
    NULL
  ),
  (
    'FS430',
    'FOIL STRIP BY 4S (BOX OF 30S)',
    NULL
  ),
  (
    'FS450',
    'FOIL STRIP BY 4S (BOX OF 50S)',
    NULL
  ),
  (
    'FS460',
    'FOIL STRIP BY 4S (BOX OF 60S)',
    NULL
  ),
  (
    'FS480',
    'FOIL STRIP BY 4S (BOX OF 80S)',
    NULL
  ),
  (
    'FS4CX',
    'FOIL STRIP BY 4S (BOX OF 100S)',
    NULL
  ),
  (
    'FS4HX',
    'FOIL STRIP BY 4S (BOX OF 100S)',
    NULL
  ),
  (
    'FS4S4',
    'FOIL STRIP BY 4S (BOX OF 40S)',
    NULL
  ),
  ('FS4XX', 'FOLI STRIP BY 4S', NULL),
  (
    'FS50X',
    'FOIL STRIP BY 50S',
    NULL
  ),
  (
    'FS60X',
    'FOIL STRIP BY 60S',
    NULL
  ),
  (
    'FS612',
    'FOIL STRIP BY 6S (BOX OF 12S)',
    NULL
  ),
  (
    'FS630',
    'FOIL STRIP BY 6S (BOX OF 30S)',
    NULL
  ),
  (
    'FS660',
    'FOIL STRIP BY 6S (BOX OF 60S)',
    NULL
  ),
  (
    'FS840',
    'FOIL STRIP BY 8S (BOX OF 40S)',
    NULL
  ),
  (
    'FS880',
    'FOIL STRIP BY 8S (BOX OF 80S)',
    NULL
  ),
  (
    'FS88X',
    'FOIL STRIP BY 8S (BOX OF 8S)',
    NULL
  ),
  (
    'FS90X',
    'FOIL STRIP BY 90S',
    NULL
  ),
  ('FSTRI', 'FOIL STRIP', NULL),
  (
    'FSTT1',
    'FOIL STRIP BY 21S',
    NULL
  ),
  (
    'FSTTW',
    'FOIL STRIP BY 20S',
    NULL
  ),
  (
    'FSTTX',
    'FOIL STRIP BY 20S (BOX OF 100S)',
    NULL
  ),
  (
    'FSTXX',
    'FOIL STRIP BY 10S',
    NULL
  ),
  ('FVIAL', 'FLINT VIAL', NULL),
  (
    'FX500',
    'FOIL STRIP BY 10S (BOX OF 500S)',
    NULL
  ),
  ('GLAMP', 'GLASS AMPUL', NULL),
  ('GVIAL', 'GLASS VIAL', NULL),
  (
    'PB100',
    'PLASTIC BOTTLE BY 100S',
    NULL
  ),
  ('PLASB', 'PLASTIC BOTTLE', NULL),
  (
    'PR100',
    'PROPYLENE BAG X 100 ML',
    NULL
  ),
  (
    'PVC10',
    'PVC BLISTER PACK BY 10S (BOX OF 100S)',
    NULL
  ),
  (
    'PVC50',
    'PVC/AL BLISTER PACK BY 10S (BOX OF 50S)',
    NULL
  ),
  ('PVCAL', 'PVC/ALU BLISTER', NULL),
  ('ROTAP', 'ROTAPLAST', NULL),
  (
    'SPT10',
    'STRIP PACK BY 10S (BOX OF 100S)',
    NULL
  ),
  (
    'SPT20',
    'STRIP PACK BY 10S (BOX OF 20S)',
    NULL
  ),
  (
    'SPT30',
    'STRIP PACK BY 10S (BOX OF 30S)',
    NULL
  ),
  (
    'SPT50',
    'STRIP PACK BY 10S (BOX OF 50S)',
    NULL
  ),
  (
    'SPT80',
    'STRIP PACK BY 10S (BOX OF 80S)',
    NULL
  ),
  ('VIALX', 'VIAL', NULL),
  ('XXXX', 'XXXX', NULL),
  ('SAC01', 'SACHET', NULL),
  ('00000', 'N/A', NULL),
  ('TUB01', 'TUBE', NULL),
  ('USP02', 'USP', NULL),
  ('GB001', 'GLASS BOTTLE', NULL),
  ('DRO01', 'DROPS', NULL),
  ('JAR01', 'JAR', NULL),
  ('PLB01', 'POUCHED LDPE BAG', NULL),
  ('DIS01', 'DISPENSER', NULL),
  (
    'BAM01',
    'BREATH ACTUATED MDI',
    NULL
  ),
  ('SOA01', 'SOAP', NULL),
  ('GC001', 'GLASS CARTRIDGE', NULL),
  (
    'PFS01',
    'PRE-FILLED SYRINGE',
    NULL
  ),
  ('DRU01', 'DRUM', NULL),
  ('INH01', 'INHALER', NULL),
  ('NEB01', 'NEBULE', NULL),
  (
    'MDI01',
    'METERED DOSE INHALER',
    NULL
  ),
  (
    'DS001',
    'DISPOSABLE SYRINGE',
    NULL
  ),
  (
    'DIC01',
    'DUPLEX III CONTAINER',
    NULL
  ),
  (
    'DPB01',
    'D5W PRE-MIXED BOTTLE',
    NULL
  ),
  ('CAN01', 'CAN', NULL),
  ('AI001', 'AUTO-INJECTOR', NULL),
  (
    'PFS02',
    'PRE-FILLED SYRINGE (HAS FREE)',
    NULL
  ),
  (
    'PFG01',
    'PRE-FILLED GLASS SYRINGE',
    NULL
  ),
  ('ELI01', 'ELIXIR', NULL),
  (
    'PSN01',
    'PRE-FILLED SYRINGE WITH NEEDLE',
    NULL
  ),
  ('IMP01', 'IMPLANT', NULL),
  ('GAL01', 'GALLON', NULL),
  (
    'PFS03',
    'PRE-FILLED SYRINGE (SINGLE DOSE)',
    NULL
  ),
  ('MV001', 'MONODOSE VIAL', NULL),
  (
    'GPS01',
    'GLASS PRE-FILLED SYRINGE',
    NULL
  ),
  (
    'VPS01',
    'VIAL + PRE-FILLED SYRINGE DILUENT',
    NULL
  ),
  (
    'SDS01',
    'SINGLE DOSE WITH SYRINGE',
    NULL
  ),
  ('VS001', 'VIAL + SYRINGE', NULL),
  ('CAR01', 'CARPULE', NULL),
  ('PRM01', 'PRE-MIXED', NULL),
  ('PLT01', 'PLASTIC TUBE', NULL),
  ('VA001', 'VIAL + AMPULE', NULL),
  ('CP001', 'COMBO-PACK ', NULL),
  ('CYC01', 'CYCLE', NULL),
  (
    'LPV01',
    'LYOPHILIZED POWDER + VIAL',
    NULL
  ),
  (
    'LPS01',
    'LYOPHILIZED POWDER + SYRINGE',
    NULL
  ),
  (
    'VPS02',
    'VIAL + PRE-FILLED SYRINGE',
    NULL
  ),
  ('POUC1', 'POUCH', NULL),
  ('CAR10', 'CARTRIDGE', NULL),
  ('CRRT1', 'CRRT', NULL) ;



