CREATE TABLE `gmmci041518`.`seg_phil_medicine_salt`(
  `salt_code` VARCHAR(5) NOT NULL DEFAULT '',
  `salt_desc` TEXT,
  `date_added` DATE,
  PRIMARY KEY (`salt_code`)
);

INSERT INTO `gmmci041518`.`seg_phil_medicine_salt` (
  `salt_code`,
  `salt_desc`,
  `date_added`
)
VALUES
  ('00000', ' N/A', NULL),
  ('00001', 'ACETATE', NULL),
  ('00002', 'HYDROCHLORIDE', NULL),
  ('00003', 'SALICYLATE', NULL),
  ('00004', 'TROMETHAMOL', NULL),
  ('00005', 'ACETONIDE', NULL),
  ('00006', 'ANHYDROUS', NULL),
  ('00007', 'AXETIL', NULL),
  ('00008', 'BENZOATE', NULL),
  ('00009', 'BESILATE', NULL),
  ('00010', 'BITARTRATE', NULL),
  ('00011', 'BROMIDE', NULL),
  ('00012', 'CALCIUM', NULL),
  ('00013', 'CHLORIDE', NULL),
  ('00014', 'CILEXETIL', NULL),
  ('00015', 'CITRATE', NULL),
  ('00016', 'DECANOATE', NULL),
  ('00017', 'DIHYDRATE', NULL),
  ('00018', 'DIHYDROCHLORIDE', NULL),
  ('00019', 'DIPROPIONATE', NULL),
  (
    '00020',
    'DISODIUM/SODIUM SALT',
    NULL
  ),
  ('00021', 'ENANTHATE', NULL),
  ('00022', 'ETHYL SUCCINATE', NULL),
  ('00023', 'FUMARATE', NULL),
  (
    '00024',
    'FUMARATE DIHYDRATE',
    NULL
  ),
  ('00025', 'FUROATE', NULL),
  ('00026', 'GLUCONATE', NULL),
  ('00027', 'HYCLATE', NULL),
  (
    '00028',
    'HYDRATE + DIPROPIONATE',
    NULL
  ),
  ('00029', 'HYDROBROMIDE', NULL),
  (
    '00030',
    'HYDROCHLORIDE DIHYDRATE',
    NULL
  ),
  (
    '00031',
    'HYDROGEN TARTRATE',
    NULL
  ),
  ('00032', 'LACTATE', NULL),
  ('00033', 'MACROCRYSTALS', NULL),
  ('00034', 'MALEATE', NULL),
  ('00035', 'MEGLUMINE', NULL),
  (
    '00036',
    'MEGLUMINE AND/OR\r\nSODIUM SALT',
    NULL
  ),
  ('00037', 'MESILATE', NULL),
  ('00038', 'MONOHYDRATE', NULL),
  (
    '00039',
    'MONOHYDROCHLORIDE',
    NULL
  ),
  (
    '00040',
    'MYCOPHENOLATE SODIUM',
    NULL
  ),
  ('00041', 'N-BUTYL BROMIDE', NULL),
  ('00042', 'NITROGLYCERINE', NULL),
  ('00043', 'OXALATE', NULL),
  ('00044', 'PALMITATE', NULL),
  (
    '00045',
    'PALMITATE HYDROCHLORIDE',
    NULL
  ),
  ('00046', 'PENTAHYDRATE', NULL),
  ('00047', 'PHOSPHATE', NULL),
  (
    '00048',
    'PHOSPHATE OR DIPHOSPHATE',
    NULL
  ),
  ('00049', 'POTASSIUM SALT', NULL),
  ('00050', 'PROPIONATE', NULL),
  ('00051', 'SODIUM', NULL),
  ('00052', 'SODIUM PHOSPHATE', NULL),
  ('00053', 'SODIUM SALT', NULL),
  ('00054', 'SODIUM SUCCINATE', NULL),
  ('00055', 'STEARATE', NULL),
  ('00056', 'TARTRATE', NULL),
  ('00057', 'TRIHYDRATE', NULL),
  ('00058', 'TYDROCHLORIDE', NULL),
  ('00059', 'VALERATE', NULL),
  ('00060', 'SULFATE', NULL) ;

