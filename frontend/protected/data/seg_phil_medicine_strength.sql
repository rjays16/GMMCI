CREATE TABLE `gmmci041518`.`seg_phil_medicine_strength`(
  `strength_code` VARCHAR(10) NOT NULL DEFAULT '',
  `strength_desc` TEXT,
  `date_added` DATE,
  PRIMARY KEY (`strength_code`)
);

INSERT INTO `gmmci041518`.`seg_phil_medicine_strength` (
  `strength_code`,
  `strength_desc`,
  `date_added`
)
VALUES
  ('00000', 'N/A', NULL),
  ('00001', '2%, 5 g', NULL),
  ('00002', '2%, 20 mL', NULL),
  ('00003', '2.5 mg', NULL),
  ('00004', '200 mg', NULL),
  ('00006', '200 mg/5 mL', NULL),
  ('00007', '20 mg', NULL),
  ('00011', '20 mL', NULL),
  ('00013', '250 mcg', NULL),
  ('00015', '250 mg', NULL),
  ('00020', '250 mL', NULL),
  ('00021', '25 mg', NULL),
  ('00025', '2 g', NULL),
  ('00026', '2 mg', NULL),
  ('00028', '300 mg', NULL),
  ('00030', '300 mg/mL', NULL),
  ('00031', '30 mg', NULL),
  ('00032', '350 mg/ml', NULL),
  ('00033', '3 mg', NULL),
  ('00034', '400 mg', NULL),
  ('00035', '400 mg + 80 mg', NULL),
  ('00036', '40 mg', NULL),
  ('00039', '40 mg/mL', NULL),
  ('00040', '450 mg', NULL),
  ('00041', '4 mg', NULL),
  ('00044', '500 mL', NULL),
  ('00045', '500 mcg', NULL),
  ('00047', '500 mg', NULL),
  ('00049', '500 mL  (glass)', NULL),
  ('00050', '50 mg', NULL),
  ('00055', '50 mL', NULL),
  ('00056', '5 mg', NULL),
  ('00060', '5 mL', NULL),
  ('00061', '600 mg', NULL),
  ('00062', '60 mg', NULL),
  ('00063', '6 mg', NULL),
  ('00064', '750 mg', NULL),
  ('00065', '75 mg', NULL),
  ('00066', '80 mg', NULL),
  ('00067', '80 mg + 12.5 mg', NULL),
  ('00068', '8 mg', NULL),
  ('00069', '10 mg', NULL),
  ('00071', '90 mg MR', NULL),
  ('00072', '3 in 1 ', NULL),
  ('00073', '4E-5', NULL),
  ('00074', '0.0001', NULL),
  ('00075', '0.0001', NULL),
  ('00078', '0.0005', NULL),
  ('00082', '0.001', NULL),
  ('00084', '0.0015', NULL),
  ('00085', '0.0025', NULL),
  ('00087', '0.25 mcg', NULL),
  ('00088', '0.3% + 0.1%', NULL),
  ('00089', '0.003', NULL),
  ('00094', '0.5 mL', NULL),
  ('00117', '1,500,000 IU', NULL),
  ('00118', '1.2 MU', NULL),
  ('00119', '1.5% Solution', NULL),
  ('00120', '1.5 g', NULL),
  ('00121', '1.5 mg', NULL),
  ('00122', '1.5 mL', NULL),
  ('00123', '1.6 mg/mL', NULL),
  ('00124', '10 IU/mL', NULL),
  ('00133', '0.1', NULL),
  ('00135', '10,000 IU', NULL),
  ('00136', '10,000 IU/2 mL', NULL),
  (
    '00137',
    '100 mcg + 50 mcg x 28 doses',
    NULL
  ),
  (
    '00138',
    '100 mcg + 50 mcg x 60 doses',
    NULL
  ),
  ('00139', '100 mg', NULL),
  ('00142', '100 mg/ml', NULL),
  ('00144', '100,000U mL', NULL),
  ('00145', '1000 IU/mL', NULL),
  ('00148', '100 mcg', NULL),
  (
    '00150',
    '100 mg + 100 mg + 1 mg',
    NULL
  ),
  (
    '00151',
    '100 mg + 100 mg + 1 mg, 3 mL',
    NULL
  ),
  ('00152', '100 mg + 25 mg', NULL),
  (
    '00153',
    '100 mg + 5 mg + 50 mcg',
    NULL
  ),
  (
    '00155',
    '100mg elem.iron+60 mg ',
    NULL
  ),
  ('00156', '100 mg/10 mL', NULL),
  ('00162', '100 mg/5 mL', NULL),
  ('00177', '100mg/mL/1g', NULL),
  ('00178', '100 mL', NULL),
  ('00179', '10 mcg/0.5 mL', NULL),
  ('00180', '10 mEq', NULL),
  ('00184', '10 mg/5 mL', NULL),
  ('00194', '10 mg/mL', NULL),
  ('00195', '10 mL', NULL),
  (
    '00196',
    '1.25 g (equiv. to 500 mg elemental calcium) + 250 IU',
    NULL
  ),
  ('00197', '12.5 mg/ 5 mL', NULL),
  ('00198', '12.5 mg/mL', NULL),
  ('00199', '120 mg', NULL),
  (
    '00200',
    '120 mg/mL (130 mg/mL)',
    NULL
  ),
  ('00201', '120 mL', NULL),
  (
    '00202',
    '125 mcg + 25 mcg x 120 doses',
    NULL
  ),
  ('00203', '125 mcg', NULL),
  (
    '00204',
    '125 mcg/dose X 120 doses',
    NULL
  ),
  ('00205', '125 mg', NULL),
  (
    '00206',
    '125 mg + 31 mg/5 mL',
    NULL
  ),
  ('00209', '125 mg/5 mL', NULL),
  ('00214', '125 mg/mL', NULL),
  ('00215', '150 mg', NULL),
  ('00216', '1500 IU/mL', NULL),
  ('00218', '150 mcg', NULL),
  ('00219', '150 mcg/0.6 mL', NULL),
  ('00220', '150 mcg/mL', NULL),
  ('00222', '150 mg + 12.5 mg', NULL),
  ('00223', '150 mg/5 mL', NULL),
  ('00224', '150 mg/mL', NULL),
  ('00227', '15 g', NULL),
  ('00228', '15 mg', NULL),
  ('00229', '15 mg/0.6 mL', NULL),
  ('00231', '15 mL', NULL),
  ('00233', '160 mg', NULL),
  ('00234', '160 mg + 12.5 mg', NULL),
  ('00235', '16 mg', NULL),
  ('00236', '180 mg', NULL),
  ('00237', '187 mg', NULL),
  ('00238', '18 mcg/dose', NULL),
  ('00240', '19 g/7 g', NULL),
  ('00241', '1 g', NULL),
  ('00242', '1 g + 4 mL', NULL),
  (
    '00243',
    '1 g/16 mL + Diluent',
    NULL
  ),
  ('00244', '1 g + 10 mL', NULL),
  ('00245', '1 g + 3.5 mL, 1%', NULL),
  ('00247', '1 L', NULL),
  ('00248', '1 L (Glass)', NULL),
  (
    '00249',
    '1 L Soln for irrigation',
    NULL
  ),
  ('00251', '1 meq/mL', NULL),
  ('00252', '1 mg', NULL),
  ('00261', '1 mg/mL', NULL),
  ('00263', '1 MU', NULL),
  (
    '00264',
    '2%, 1.8mL w/ epinephrine',
    NULL
  ),
  ('00265', '2%, 15 g', NULL),
  ('00266', '2%, 20 g', NULL),
  ('00267', '2%, 1.5 g', NULL),
  ('00268', '2%, 10 mL', NULL),
  ('00271', '2%, 15 mL', NULL),
  ('00272', '2%, 2 mL', NULL),
  ('00273', '2%, 30 g', NULL),
  ('00274', '2%, 50 mL', NULL),
  ('00276', '2%, 5 mL', NULL),
  ('00279', '0.025', NULL),
  ('00280', '2.5 IU/0.5 mL', NULL),
  (
    '00281',
    '2.5 IU/mL, 1 dose + 1 mL diluent',
    NULL
  ),
  ('00283', '2.5 mEq/mL', NULL),
  ('00284', '2.5 mg/0.5 mL', NULL),
  ('00289', '0.2', NULL),
  ('00293', '200 IU/mL', NULL),
  ('00294', '200,000 IU', NULL),
  ('00295', '2000 IU/ 0.3 mL', NULL),
  ('00296', '2000 IU/0.5 mL', NULL),
  ('00297', '2000 IU/mL', NULL),
  ('00298', '200-400 IU/mL', NULL),
  (
    '00299',
    '200 mcg/dose, 60 doses',
    NULL
  ),
  (
    '00301',
    '200 mg + 28.5 mg/5 mL',
    NULL
  ),
  ('00311', '20 mg + 12.5 mg', NULL),
  ('00312', '20 mg FDT', NULL),
  ('00313', '20 mg/0.5 mL', NULL),
  ('00314', '20 mg/5 mL', NULL),
  (
    '00320',
    '225 mg + 200 mg/5 mL',
    NULL
  ),
  ('00321', '240 mg', NULL),
  ('00322', '240 mL', NULL),
  ('00323', '24 mg', NULL),
  (
    '00327',
    '250 mcg + 25 mcg x 120 doses',
    NULL
  ),
  (
    '00328',
    '250 mcg + 50 mcg x 28 doses',
    NULL
  ),
  (
    '00329',
    '250 mcg + 50 mcg x 60 doses',
    NULL
  ),
  ('00331', '2500 mg', NULL),
  ('00334', '250 mg + 125 mg', NULL),
  ('00335', '250 mg + 25 mg', NULL),
  (
    '00336',
    '250 mg + 62.5 mg/5 mL',
    NULL
  ),
  ('00337', '250 mg + 2 mL', NULL),
  ('00340', '250 mg + 5 mL', NULL),
  ('00341', '25 mcg', NULL),
  ('00342', '25 mcg/0.5 mL', NULL),
  ('00343', '25 mg + 2 mL', NULL),
  ('00345', '27.5 mg/mL', NULL),
  ('00346', '2 g + 250 mg', NULL),
  ('00349', '2 mg/5 mL', NULL),
  ('00350', '2 mg/5mL', NULL),
  ('00357', '2 mg/ml', NULL),
  ('00361', '2 mL', NULL),
  ('00364', '0.03', NULL),
  ('00366', '0.035', NULL),
  ('00367', '30 g', NULL),
  ('00369', '300 mg + 250 mcg', NULL),
  ('00370', '300 mcg/1.2 mL', NULL),
  ('00371', '300 mcg/mL', NULL),
  ('00372', '300 mg + 12.5 mg', NULL),
  ('00374', '300 mg/5 mL', NULL),
  ('00375', '30 mcg + 150 mcg', NULL),
  ('00377', '30 mg/5 mL', NULL),
  ('00379', '30 mL', NULL),
  ('00381', '320 mg/mL', NULL),
  ('00382', '35 mg', NULL),
  ('00383', '360 mg', NULL),
  ('00385', '370 mg/mL', NULL),
  ('00386', '374 mg', NULL),
  ('00387', '3 mg/mL', NULL),
  ('00388', '3 mL', NULL),
  ('00389', '4% solution', NULL),
  (
    '00390',
    '4.25% Solution Dextrose',
    NULL
  ),
  ('00391', '0.045', NULL),
  ('00392', '4.5 mg', NULL),
  ('00393', '4.6 mg', NULL),
  ('00394', '400 IU/ml', NULL),
  ('00395', '4000 IU/0.3 mL', NULL),
  ('00396', '4000 IU/0.4 mL', NULL),
  ('00398', '400 mcg/mL', NULL),
  (
    '00399',
    '400 mg + 57 mg/5 ml',
    NULL
  ),
  (
    '00400',
    '400 mg + 80 mg/5 mL',
    NULL
  ),
  ('00401', '40 mEq/mL', NULL),
  ('00402', '40 mg + 10 mL', NULL),
  ('00403', '40 mg + 12.5 mg', NULL),
  ('00406', '450 mg MR', NULL),
  ('00407', '47.5 mg', NULL),
  ('00409', '48 g/18 g', NULL),
  ('00411', '4 g + 500 mg', NULL),
  ('00412', '4 mg/mL + 1 mL', NULL),
  ('00414', '4 mg/mL', NULL),
  (
    '00421',
    '500 mcg + 50 mcg x 28 doses',
    NULL
  ),
  (
    '00422',
    '500 mcg + 50 mcg x 60 doses',
    NULL
  ),
  ('00423', '5000 iu /mL', NULL),
  ('00424', '5000IU/0.3ml', NULL),
  (
    '00425',
    '500mcg + 2.5mg x 2.5 mL',
    NULL
  ),
  ('00426', '500mcg powder', NULL),
  ('00427', '500mcg/ml', NULL),
  ('00429', '500mcg/mL', NULL),
  (
    '00430',
    '500mcg+ 2.5mg/2mL',
    NULL
  ),
  ('00431', '500mg + 250 mg ', NULL),
  ('00432', '500mg +2mL', NULL),
  ('00433', '500 mg MR', NULL),
  (
    '00434',
    '500mg/7.7mL + Diluent',
    NULL
  ),
  ('00435', '500mg/mL', NULL),
  ('00436', '500mg+5mL', NULL),
  ('00437', '50mcg', NULL),
  (
    '00438',
    '50mcg + 25mcg x 120 doses',
    NULL
  ),
  (
    '00439',
    '50mcg/dose X 120 doses',
    NULL
  ),
  ('00443', '50mcg/mL', NULL),
  ('00444', '50mg +12.5 mg', NULL),
  ('00445', '50mg MR', NULL),
  ('00452', '55 mg/mL', NULL),
  ('00453', '5 gm', NULL),
  ('00454', '5 mg/5 mL', NULL),
  ('00459', '5 mg/mL', NULL),
  ('00460', '5 MU', NULL),
  (
    '00461',
    '5 TU/0.1 mL vial + 2 mL Diluent',
    NULL
  ),
  ('00462', '0.06', NULL),
  (
    '00464',
    '6%, Dextran 70 in 0.9% NaCl ',
    NULL
  ),
  (
    '00465',
    '6%, Dextran 70 in 5% Dextrose',
    NULL
  ),
  ('00466', '6.25 mg', NULL),
  (
    '00468',
    '60 mg elemental iron+400 mcg',
    NULL
  ),
  ('00469', '60 mL', NULL),
  ('00470', '625 mcg', NULL),
  ('00472', '67 mg', NULL),
  ('00474', '6 mg/mL', NULL),
  ('00475', '0.07', NULL),
  ('00478', '7.5 mg/5 mL', NULL),
  ('00479', '0.7', NULL),
  ('00481', '70/30, 100 iu/mL', NULL),
  ('00482', '70 mg', NULL),
  ('00483', '75 replacement', NULL),
  (
    '00485',
    '750 mg (equiv. to 300 mg elemental calcium) + 150 IU',
    NULL
  ),
  ('00486', '75 mcg', NULL),
  ('00488', '75 mg + 150 mg', NULL),
  (
    '00489',
    '75 mg + 150 mg + 275 mg',
    NULL
  ),
  (
    '00490',
    '75 mg + 150 mg + 400 mg + 275 mg',
    NULL
  ),
  ('00491', '75 mg/5 mL', NULL),
  ('00493', '0.085', NULL),
  ('00494', '800 mcg/mL', NULL),
  ('00495', '800 mg', NULL),
  ('00496', '800 mg+160 mg', NULL),
  ('00498', '80U/0.5 mL', NULL),
  ('00499', '850g', NULL),
  ('00500', '0.0912', NULL),
  ('00502', '9.5 mg', NULL),
  ('00503', '90 mg', NULL),
  (
    '00504',
    'monodose + 0.5 mL',
    NULL
  ),
  ('00505', 'ORS 75', NULL),
  ('00507', '2.5mg/ml', NULL),
  ('00508', '200 mcg', NULL),
  ('00512', '0.5', NULL),
  ('00516', '1 mL', NULL),
  ('00517', '95% USP Grade', NULL),
  ('00518', '70 mg + 2800 IU', NULL),
  ('00521', '600 mg/5 mL', NULL),
  (
    '00526',
    '150 mg - 650 mg iodine/mL',
    NULL
  ),
  (
    '00527',
    '150 mg - 650 mg iodine/mL',
    NULL
  ),
  ('00538', '30 mg MR', NULL),
  ('00541', '0.114', NULL),
  (
    '00545',
    '750 mg durules (equiv. to approximately 10 mEq)',
    NULL
  ),
  ('00548', '25 mg/5 mL', NULL),
  ('00549', '4000 IU/mL', NULL),
  ('00550', '20 mg + 120 mg', NULL),
  ('00551', '325 mg', NULL),
  ('00552', '600 mcg', NULL),
  ('00553', '600 mcg/mL', NULL),
  (
    '00554',
    '200 units + 3 mg + 4000 units/g',
    NULL
  ),
  (
    '00556',
    '400 units + 5 mg + 5000 units/g',
    NULL
  ),
  ('00557', '340 g', NULL),
  ('00558', '454 g', NULL),
  ('00561', '100 mcg/0.1 mL', NULL),
  (
    '00562',
    '500 mcg/mL + 1 mL diluent',
    NULL
  ),
  ('00563', '100 mcg/dose', NULL),
  ('00564', '200 mcg/dose', NULL),
  ('00565', '400 mcg/dose', NULL),
  (
    '00566',
    '50 mcg/dose x 100 doses',
    NULL
  ),
  (
    '00567',
    '50 mcg/dose x 200 doses',
    NULL
  ),
  (
    '00568',
    '250 mcg/dose x 200 doses',
    NULL
  ),
  (
    '00569',
    '100 mcg/dose x 200 doses',
    NULL
  ),
  ('00570', '6% + 3%', NULL),
  ('00571', '6% +3%', NULL),
  ('00588', '0.25', NULL),
  (
    '00590',
    '5 mg (as dipropionate) + 2 mg (as sodium phosphate) per mL',
    NULL
  ),
  (
    '00593',
    '70% isophane suspension + 30% soluble insulin in 100 IU/mL',
    NULL
  ),
  ('00594', '5 mg MR', NULL),
  (
    '00596',
    '200 mcg/dose x 300 doses',
    NULL
  ),
  (
    '00597',
    '400 mcg/dose x 50 doses',
    NULL
  ),
  (
    '00598',
    '100 mcg/dose x 50 metered doses',
    NULL
  ),
  (
    '00599',
    '100 mcg/dose x 200 metered doses',
    NULL
  ),
  (
    '00600',
    '200 mcg/dose x 100 doses',
    NULL
  ),
  ('00602', '80 mcg + 4.5 mcg', NULL),
  (
    '00604',
    '160 mcg + 4.5 mcg',
    NULL
  ),
  ('00605', '320 mcg + 9 mcg', NULL),
  ('00608', '0.5% (isobaric)', NULL),
  ('00611', '0.08', NULL),
  (
    '00612',
    '70% solution, 500 mL',
    NULL
  ),
  ('00614', '50 mcg/g', NULL),
  ('00615', '50 mcg/mL', NULL),
  (
    '00616',
    '50 mcg + 500 mcg/g',
    NULL
  ),
  ('00617', '0.5 mcg', NULL),
  (
    '00618',
    '1.437 g calcium glubionate and 295 mg calcium lactobionate (equiv. to 110mg ionizable calcium),120mL',
    NULL
  ),
  (
    '00619',
    '25 mg (equiv. to 25 mg folinic acid)',
    NULL
  ),
  ('00622', '7.5 mg/mL', NULL),
  ('00626', '200 mg MR', NULL),
  ('00627', '400 mg MR', NULL),
  (
    '00630',
    '100 mg + 3 mL diluent',
    NULL
  ),
  (
    '00636',
    '250 mg + 2 mL diluent',
    NULL
  ),
  (
    '00637',
    '500 mg + 2 mL diluent',
    NULL
  ),
  (
    '00638',
    '1 g + 4 mL diluent',
    NULL
  ),
  (
    '00639',
    '250 mg + 2 mL 1% solution of lidocaine',
    NULL
  ),
  (
    '00640',
    '500 mg + 2 mL 1% solution of lidocaine',
    NULL
  ),
  (
    '00641',
    '250 mg + 5 mL diluent',
    NULL
  ),
  (
    '00642',
    '500 mg + 5 mL diluent',
    NULL
  ),
  (
    '00643',
    '1 g + 3.5 mL 1% solution of lidocaine',
    NULL
  ),
  (
    '00644',
    '1 g + 10 mL diluent',
    NULL
  ),
  ('00645', '500 mg/5 mL', NULL),
  ('00649', '0.005', NULL),
  ('00655', '0.0012', NULL),
  ('00658', '0.04', NULL),
  ('00663', '2.5 mg/5 mL', NULL),
  ('00668', '500 mcg/mL', NULL),
  ('00679', '800 MU/4.8 mL', NULL),
  ('00680', '800 IU/mL', NULL),
  ('00682', '4 g', NULL),
  ('00683', '1.25 mg', NULL),
  ('00684', '625 mcg + 2.5 mg', NULL),
  ('00685', '625 mcg + 5 mg', NULL),
  (
    '00686',
    '25 mg powder + 5 mL diluent',
    NULL
  ),
  ('00687', '300 mcg', NULL),
  ('00694', '2500 IU/0.2 mL', NULL),
  ('00695', '5000 IU/0.2 mL', NULL),
  ('00696', '10000 IU/mL', NULL),
  (
    '00697',
    '20 mg (with 3 g mannitol/vial)',
    NULL
  ),
  ('00699', '15 mcg/mL', NULL),
  ('00700', '750 mcg', NULL),
  (
    '00701',
    '10% dextran 40 in 0.9% NaCl',
    NULL
  ),
  (
    '00702',
    '10% dextran 40 in 5% dextrose',
    NULL
  ),
  ('00706', '15 mg/5 mL', NULL),
  ('00708', '5 mg/2.5 mL', NULL),
  ('00709', '10 mg/2.5 mL', NULL),
  ('00710', '50 mg MR', NULL),
  ('00711', '75 mg MR', NULL),
  ('00712', '100 mg MR', NULL),
  ('00713', '37.5 mg/ml', NULL),
  ('00715', '250 mg MR', NULL),
  ('00717', '60 mg MR', NULL),
  ('00718', '180 mg MR', NULL),
  ('00719', '469 mg/mL', NULL),
  ('00725', '12.5 mg/5 mL', NULL),
  ('00730', '20,000 IU', NULL),
  (
    '00732',
    '30 IU diphtheria toxoid + 40 IU tetanus toxoid per 0.5 mL',
    NULL
  ),
  (
    '00733',
    '2 IU diphtheria toxoid + 20 IU tetanus toxoid',
    NULL
  ),
  ('00735', '0.5 mL', NULL),
  ('00737', '0.1% - 2%', NULL),
  (
    '00738',
    '1 mg/mL, 250 mL D5W',
    NULL
  ),
  (
    '00740',
    '4 mg/mL, 250 mL D5W',
    NULL
  ),
  ('00741', '80 mg/mL', NULL),
  ('00742', '3.2 mg/mL', NULL),
  ('00743', '800 mcg/mL', NULL),
  (
    '00744',
    '0.5 mL DTP diluent + 10 mcg Hib',
    NULL
  ),
  ('00745', '0.5 mL monodose', NULL),
  ('00747', '10,000 IU/mL', NULL),
  ('00748', '40,000 IU/mL', NULL),
  ('00749', '10000 IU/0.6 mL', NULL),
  ('00750', '20000 IU/mL', NULL),
  ('00751', '30000 IU/0.6 mL', NULL),
  (
    '00752',
    '1.25 mg (50,000 IU)',
    NULL
  ),
  (
    '00753',
    '250 mcg/mL (10,000 IU/mL)',
    NULL
  ),
  ('00757', '400 mg/5 mL', NULL),
  ('00758', '35 mcg + 400 mcg', NULL),
  ('00759', '30 mcg + 300 mcg', NULL),
  ('00761', '100 IU/mL', NULL),
  ('00763', '500 IU + diluent', NULL),
  (
    '00764',
    '100 IU/g + diluent',
    NULL
  ),
  ('00765', '10 mg MR', NULL),
  ('00767', '100 mcg/mL', NULL),
  ('00770', '9 mg', NULL),
  ('00771', '250 mcg/mL', NULL),
  ('00772', '500 mcg/15 mL', NULL),
  ('00774', '50 mcg/dose', NULL),
  ('00775', '250 mcg/dose', NULL),
  (
    '00776',
    '50 mcg/dose x 60 doses',
    NULL
  ),
  (
    '00777',
    '50 mcg/dose x 120 doses',
    NULL
  ),
  (
    '00778',
    '125 mcg/dose x 60 doses',
    NULL
  ),
  (
    '00782',
    '0.05% dose x 120 doses',
    NULL
  ),
  (
    '00783',
    '50 mcg + 500 mcg x 60 doses',
    NULL
  ),
  ('00784', '1 g/mL', NULL),
  ('00785', '30 mg/mL', NULL),
  ('00788', '287 mg/mL', NULL),
  ('00792', '8 mg MR', NULL),
  ('00793', '16 mg MR', NULL),
  ('00794', '24 mg MR', NULL),
  (
    '00799',
    '1 mg lyophilized powder + solvent',
    NULL
  ),
  ('00802', '50% (85 Kcal)', NULL),
  ('00805', '2.5 g', NULL),
  ('00808', '400 mcg', NULL),
  ('00809', '3.6 mg', NULL),
  ('00810', '10.8 mg', NULL),
  (
    '00811',
    '5 gallon (concentrate)',
    NULL
  ),
  (
    '00813',
    '10 mcg/0.5 mL, 1 dose + 0.5 mL diluent with tetanus protein',
    NULL
  ),
  (
    '00814',
    '10 mcg/0.5 mL + 0.9% Sodium Chloride with Diphtheria CRM 197 protein',
    NULL
  ),
  (
    '00815',
    '0.5 mL with Meningococcal Protein',
    NULL
  ),
  (
    '00816',
    'viral antigen not less than 720 EU in 0.5 mL (junior)',
    NULL
  ),
  (
    '00817',
    'viral antigen not less than 1440 EU in 1 mL (adult)',
    NULL
  ),
  (
    '00818',
    '80 units/0.5 mL (pediatric)',
    NULL
  ),
  (
    '00819',
    '160 units/0.5 mL (adult)',
    NULL
  ),
  (
    '00822',
    '10 mcg/0.5 mL (pediatric)',
    NULL
  ),
  (
    '00823',
    '20 mcg/mL (adult)',
    NULL
  ),
  ('00824', '20 mcg/mL', NULL),
  (
    '00826',
    '500 IU lyophilized powder + 1 mL solvent',
    NULL
  ),
  (
    '00827',
    '1000 IU lyophilized powder + 1 mL solvent',
    NULL
  ),
  (
    '00828',
    '5000 IU lyophilized powder + 1 mL solvent',
    NULL
  ),
  (
    '00829',
    '5 mg lyophilized powder + 5 mL diluent',
    NULL
  ),
  (
    '00830',
    '4 mg lyophilized powder + 2 mL diluent',
    NULL
  ),
  (
    '00831',
    '75 IU FSH + 75 IU LH + 1 mL',
    NULL
  ),
  ('00838', '800 mg MR', NULL),
  ('00841', '1%-2%', NULL),
  ('00842', '6 mL', NULL),
  ('00847', '160 mg/mL', NULL),
  ('00851', '50 mg/mL', NULL),
  (
    '00853',
    '1 g freeze-dried power + 33 mL reconstitution fluid',
    NULL
  ),
  (
    '00854',
    '1 g freeze-dried power + 50 mL reconstitution fluid',
    NULL
  ),
  (
    '00855',
    '2.5 g freeze-dried power + 100 mL reconstitution fluid',
    NULL
  ),
  (
    '00856',
    '5 g freeze-dried powder + 100 mL diluent vial',
    NULL
  ),
  (
    '00857',
    '10 g freeze-dried power + 260 mL diluent vial',
    NULL
  ),
  ('00858', '1.5 mg MR', NULL),
  (
    '00860',
    '0.5 mL Suspension',
    NULL
  ),
  ('00861', '200 IU', NULL),
  ('00862', '3 million IU', NULL),
  ('00864', '4.5 million IU', NULL),
  ('00865', '9 million IU', NULL),
  (
    '00866',
    '4.5 million IU/0.5 mL',
    NULL
  ),
  (
    '00867',
    '6 million IU/0.5 mL',
    NULL
  ),
  (
    '00868',
    '3 million IU/mL + diluent',
    NULL
  ),
  (
    '00869',
    '5 million IU/mL + diluent',
    NULL
  ),
  (
    '00870',
    '10 million IU/mL + diluent',
    NULL
  ),
  (
    '00874',
    '495 mg/mL equiv. to 300 mg/mL iodine',
    NULL
  ),
  (
    '00875',
    '496 mg/mL equiv. to 300 mg/mL iodine',
    NULL
  ),
  (
    '00878',
    '627.9 mg/mL equiv. to 380 mg/mL iodine',
    NULL
  ),
  ('00880', '180 mg iodine/mL', NULL),
  ('00881', '240 mg iodine/mL', NULL),
  ('00882', '300 mg iodine/mL', NULL),
  ('00884', '350 mg iodine/mL', NULL),
  (
    '00887',
    '408 mg/mL equiv. to 200 mg iodine',
    NULL
  ),
  (
    '00890',
    '612 mg/mL equiv. to 300 mg iodine',
    NULL
  ),
  (
    '00893',
    '755 mg/mL equiv. to 370 mg iodine',
    NULL
  ),
  (
    '00895',
    '240 mg/mL equiv. to 499 mg iodine',
    NULL
  ),
  (
    '00900',
    '300 mg/mL equiv. to 623 mg iodine',
    NULL
  ),
  (
    '00902',
    '370 mg/mL equiv. to 769 mg iodine',
    NULL
  ),
  ('00905', '600 mg/mL', NULL),
  (
    '00908',
    '636 mg/mL equiv. to 300 mg/mL iodine',
    NULL
  ),
  (
    '00910',
    '678 mg/mL equiv. to 320 mg/mL iodine',
    NULL
  ),
  (
    '00912',
    '741 mg/mL equiv. to 350 mg/mL iodine',
    NULL
  ),
  (
    '00915',
    '9.66 g sodium ioxithalamate + 65.09 g meglumine ioxithalamate ( equiv. to 35 g iodine)',
    NULL
  ),
  (
    '00917',
    '19.81 g meglumine ioxithalamate (equiv. to 9 g iodine)',
    NULL
  ),
  (
    '00918',
    '33.015 g meglumine ioxithalamate (equiv. to 15 g iodine)',
    NULL
  ),
  (
    '00919',
    '66.03 g meglumine ioxithalamate (equiv. to 30 g iodine)',
    NULL
  ),
  (
    '00920',
    '20 mcg/dose x 200 doses',
    NULL
  ),
  ('00923', '250 mcg/mL', NULL),
  (
    '00924',
    '20 mcg + 50 mcg x 10 mL doses',
    NULL
  ),
  (
    '00925',
    '250 mcg + 500 mcg per mL',
    NULL
  ),
  (
    '00926',
    '500 mcg + 1.25 mg per 4 mL (unit dose)',
    NULL
  ),
  (
    '00927',
    '500 mcg + 2.5 mg x 2.5 mL (unit dose)',
    NULL
  ),
  (
    '00928',
    '21 mcg + 120 mcg x 200 doses x 10 mL',
    NULL
  ),
  ('00929', '40 mg/2 mL', NULL),
  ('00931', '150 mg + 400 mg', NULL),
  ('00932', '200 mg + 500 mg', NULL),
  (
    '00933',
    '30 mg + 60 mg (pediatric)',
    NULL
  ),
  (
    '00934',
    '60 mg + 60 mg (pediatric)',
    NULL
  ),
  ('00935', '150 mg + 150 mg', NULL),
  ('00936', '100 mg + 150 mg', NULL),
  ('00937', '150 mg + 300 mg', NULL),
  ('00938', '200 mg + 225 mg', NULL),
  ('00939', '300 mg + 450 mg', NULL),
  ('00940', '400 mg + 450 mg', NULL),
  ('00941', '600 mg + 400 mg', NULL),
  (
    '00942',
    '30 mg + 60 mg + 150 mg (pediatric)',
    NULL
  ),
  (
    '00943',
    '75 mg + 150 mg + 400 mg',
    NULL
  ),
  (
    '00944',
    '150 mg + 150 mg + 500 mg',
    NULL
  ),
  (
    '00945',
    '300 mg + 450 mg + 500 mg',
    NULL
  ),
  (
    '00946',
    '60 mg + 120 mg + 300 mg + 225 mg',
    NULL
  ),
  (
    '00947',
    '200 mg + 450 mg + 500 mg + 400 mg',
    NULL
  ),
  ('00948', '300 mg + 150 mg', NULL),
  ('00949', '20 mg MR', NULL),
  ('00950', '40 mg MR', NULL),
  ('00951', '3.3 g/5 mL', NULL),
  ('00952', '15 mg MR', NULL),
  ('00953', '3.75 mg/2 mL', NULL),
  ('00954', '1.88 mg', NULL),
  ('00955', '3.75 mg', NULL),
  ('00956', '11.25 mg', NULL),
  (
    '00959',
    '2%, 5 mL (epidural, local infiltration)',
    NULL
  ),
  (
    '00960',
    '2%, 10 mL (epidural, local infiltration)',
    NULL
  ),
  (
    '00961',
    '2%, 50 mL (epidural, local infiltration)',
    NULL
  ),
  (
    '00967',
    'monodose vial + 0.5 mL diluent',
    NULL
  ),
  (
    '00968',
    'multidose vial + 5 mL diluent',
    NULL
  ),
  ('00969', '1 mL (10) doses', NULL),
  ('00970', '2 mL (20 doses)', NULL),
  (
    '00971',
    'not less than 2000 PFU freeze-dried powder monodose vial + diluent (0.5 mL water for injection)',
    NULL
  ),
  ('00972', '250 mg/mL', NULL),
  ('00973', '500 mg/mL', NULL),
  ('00974', '12.5 mg', NULL),
  (
    '00975',
    '50 mcg/0.5 mL dose (Group A + C) multidose (10 doses) lyophilized powder + 5 mL diluent',
    NULL
  ),
  (
    '00976',
    '50 mcg/0.5 mL dose(Group A + C) single dose + 0.5 mL diluent',
    NULL
  ),
  (
    '00977',
    '50 mcg/0.5 mL dose (Serogroup A + Serogroup B + Serogroup W135 + Serogroup Y) multidose + diluent',
    NULL
  ),
  ('00978', '850 mg', NULL),
  ('00980', '2.5 mg/ml', NULL),
  ('00985', '25 mg/mL', NULL),
  (
    '00989',
    '1% solution 55 mg',
    NULL
  ),
  (
    '00990',
    '1% solution 65 mg',
    NULL
  ),
  ('00991', '18 mg', NULL),
  ('00992', '36 mg', NULL),
  ('00994', '62.5 mg/mL', NULL),
  (
    '00995',
    '125 mg lyophilized powder',
    NULL
  ),
  (
    '00996',
    '500 mg lyophilized powder',
    NULL
  ),
  (
    '00998',
    '500 mg/8.0 mL + diluent vial',
    NULL
  ),
  (
    '00999',
    '1 g lyophilized powder',
    NULL
  ),
  (
    '01001',
    '48 g/18 g per 100 mL solution',
    NULL
  ),
  (
    '01002',
    '19 g/ 7 g solution per 133 mL',
    NULL
  ),
  (
    '01003',
    '19 g/ 7 g solution per 66 mL',
    NULL
  ),
  ('01004', '15 mg/mL', NULL),
  (
    '01005',
    'crystals, 25 g/bottle',
    NULL
  ),
  ('01006', '950 IU', NULL),
  (
    '01013',
    '50 mg/scoop (1 g)',
    NULL
  ),
  (
    '01014',
    '3.5 mg neomycin + 10,000 units polymyxin B + 0.025% floucinolone acetonide/ml',
    NULL
  ),
  ('01016', '75 mg/mL', NULL),
  ('01018', '200 mcg/mL', NULL),
  ('01019', '10 mg solution', NULL),
  ('01021', '100,000 units/mL', NULL),
  ('01022', '500,000 units', NULL),
  ('01023', '100,000 units', NULL),
  (
    '01025',
    '40 mg powder vial + 10 mL solvent',
    NULL
  ),
  ('01026', '12 mg/mL', NULL),
  (
    '01027',
    '100 mg oxantel + 100 mg pyrantel',
    NULL
  ),
  (
    '01028',
    '100 mg oxantel + 100 mg pyrantel per 5 mL',
    NULL
  ),
  ('01032', '50 mg powder', NULL),
  ('01033', '0.0003', NULL),
  ('01036', '5 IU/mL', NULL),
  ('01040', '100 mg/15 mL', NULL),
  (
    '01042',
    '120 mg/5 mL (125 mg/5 mL)',
    NULL
  ),
  ('01044', '135 mcg/0.5 mL', NULL),
  ('01045', '180 mcg/0.5 mL', NULL),
  ('01046', '1,200,000 units', NULL),
  ('01047', '2,400,000 units', NULL),
  (
    '01051',
    'Sterile with 1.5% dextrose',
    NULL
  ),
  (
    '01055',
    'Sterile with 2.5% dextrose',
    NULL
  ),
  (
    '01059',
    'Sterile with 4.25% dextrose',
    NULL
  ),
  ('01062', '0.01', NULL),
  ('01063', '25 g', NULL),
  ('01064', '100 g', NULL),
  ('01065', '200 g', NULL),
  ('01066', '2 mg/0.2 mL', NULL),
  ('01070', '7-valent', NULL),
  (
    '01071',
    '25 mcg/0.5 mL, 0.5 mL',
    NULL
  ),
  (
    '01072',
    '3.5% colloidal solution',
    NULL
  ),
  ('01075', '1 mmol/mL', NULL),
  ('01077', '2 mEq/mL', NULL),
  ('01083', '0.075', NULL),
  ('01089', '20 mg/5mL', NULL),
  ('01090', '26.3 mg', NULL),
  ('01092', '80 mg MR', NULL),
  ('01095', '150 IU/mL', NULL),
  ('01099', '25,000 IU', NULL),
  ('01100', '50,000IU', NULL),
  ('01103', '100,000 IU', NULL),
  (
    '01106',
    '25 mg MR powder for suspension + 2 mL diluent',
    NULL
  ),
  (
    '01107',
    '37.5 mg MR powder for suspension + 2 mL diluent',
    NULL
  ),
  ('01109', '4 mg MR', NULL),
  (
    '01110',
    '100 mcg/dose x 300 doses',
    NULL
  ),
  (
    '01111',
    '100 mcg/dose x 400 doses',
    NULL
  ),
  ('01117', '0.17', NULL),
  ('01123', '200 mg/mL', NULL),
  ('01124', '2.5 mL', NULL),
  ('01125', '200 mL', NULL),
  ('01126', '3.5 mg', NULL),
  ('01127', '8.68 mg', NULL),
  ('01129', '17 mg', NULL),
  ('01131', '0.8 to 100 mCi', NULL),
  ('01132', '1.0 to 250 mCi', NULL),
  ('01133', '3.5 to 150 mCi', NULL),
  ('01135', '337 mcg/3 g', NULL),
  ('01136', '750,000 IU', NULL),
  ('01137', '1 g/5 mL', NULL),
  ('01140', '10% + 0.25%', NULL),
  ('01141', '500 mg + 25 mg', NULL),
  ('01143', '100 mg + 224 mg', NULL),
  ('01145', '1.5 mg/5 mL', NULL),
  (
    '01147',
    '500 mcg/dose x 100 doses',
    NULL
  ),
  ('01152', '250 IU/mL', NULL),
  ('01154', '250 units/mL', NULL),
  ('01158', '125 mg MR', NULL),
  ('01159', '300 mg MR', NULL),
  ('01160', '1 g + 50 mL', NULL),
  (
    '01163',
    '10,000 anti-XA IU/mL',
    NULL
  ),
  ('01164', '150 mg MR', NULL),
  ('01165', '2 TU/0.1 mL', NULL),
  (
    '01166',
    '5 TU/0.1 mL + 2 mL diluent',
    NULL
  ),
  (
    '01167',
    'live-attenuated S. typhi (not less than 109) viable strain',
    NULL
  ),
  (
    '01168',
    'Vi-capsular polysaccharide  S. typhi 25 mcg',
    NULL
  ),
  (
    '01169',
    '125 units/1.25 mL',
    NULL
  ),
  (
    '01170',
    '20 pressor units/mL',
    NULL
  ),
  (
    '01171',
    '4 mg/mL + 1 mL solvent',
    NULL
  ),
  ('01173', '240 mg MR', NULL),
  (
    '01174',
    '2.5 IU/0.5 mL + diluent',
    NULL
  ),
  ('01175', '2.5 IU/mL', NULL),
  ('01176', '1 mg + diluent', NULL),
  ('01177', '2 mg + diluent', NULL),
  (
    '01179',
    '10 mg + 5 mg + 5 mcg/0.6 mL',
    NULL
  ),
  (
    '01180',
    '100 mg + 100 mg + 1 mg/3 mL',
    NULL
  ),
  (
    '01181',
    '100 mg + 100 mg + 1 mg/10 mL',
    NULL
  ),
  (
    '01182',
    '1000 DL 50 mouse min (attenuated) + 0.5 mL solvent',
    NULL
  ),
  ('01183', '375 mcg', NULL),
  ('01184', '250 mg/50 mL', NULL),
  (
    '01185',
    'freeze-dried powder, 10 mL',
    NULL
  ),
  (
    '01186',
    'emulsion, 10 mL (pedia)',
    NULL
  ),
  (
    '01187',
    'emulsion, 10 mL (adult)',
    NULL
  ),
  (
    '01188',
    '470 mg NaHCO3 + 70 mg Glutamic Acid + 420 mg Tartaric Acid + 25 mg Silicon Resin',
    NULL
  ),
  (
    '01189',
    'equiv. to 60 mg elemental iron',
    NULL
  ),
  (
    '01190',
    '(equiv. to 15 mg elemental iron/0.6 mL)',
    NULL
  ),
  (
    '01192',
    '(equiv. to 30 mg elemental iron/5 mL)',
    NULL
  ),
  (
    '01193',
    '60 mg elemental iron + 400 mcg folic acid',
    NULL
  ),
  ('01194', '200 mg/50 mg', NULL),
  ('01195', '150 mg/300 mg', NULL),
  (
    '01196',
    '150 mg/300 mg/200 mg',
    NULL
  ),
  (
    '01197',
    '300 mg/600 mg/300 mg',
    NULL
  ),
  ('01198', '300 mg/300 mg', NULL),
  ('01199', '20 mg/mL', NULL),
  ('01200', '80 mg/4 mL', NULL),
  (
    '01202',
    '5000 units + 400 units + 5 mg/g',
    NULL
  ),
  (
    '01205',
    '25 mcg/0.5 mL, 2.5 mL  (multidose)',
    NULL
  ),
  ('01206', '1000 mg + 500 mg', NULL),
  ('01207', '2 g + 1 g', NULL),
  (
    '01208',
    '200 mcg/dose x 200 doses',
    NULL
  ),
  (
    '01210',
    '500 mcg + 2.5 mg x 20 mL (unit dose)',
    NULL
  ),
  (
    '01211',
    '1 mg/mL x 2.5 mL (unit dose)',
    NULL
  ),
  ('01212', '15 mcg/0.5 mL', NULL),
  ('01213', '88 mcg', NULL),
  ('01214', '112 mcg', NULL),
  ('01215', '137 mcg', NULL),
  ('01216', '145 mg', NULL),
  ('01218', '300 mg + 25 mg', NULL),
  (
    '01220',
    '200 mg + 40 mg/5 mL',
    NULL
  ),
  (
    '01221',
    '40 mg ( single dose)',
    NULL
  ),
  ('01223', '200 mg + 50 mg', NULL),
  (
    '01225',
    '50 mg prolonged-release powder + 2 mL diluent',
    NULL
  ),
  (
    '01226',
    '200 mg lyophilized powder for solution',
    NULL
  ),
  ('01228', '50 mg/5 mL', NULL),
  ('01233', '7.5 mcg/0.25 mL', NULL),
  (
    '01234',
    '0.25 mL (single dose)',
    NULL
  ),
  ('01235', '0.3 mg', NULL),
  ('01238', '88 mg', NULL),
  ('01241', '500 mg + 125 mg', NULL),
  (
    '01243',
    '2% (with alkaline activating solution)',
    NULL
  ),
  ('01244', 'USP Grade', NULL),
  ('01245', '5 g', NULL),
  (
    '01247',
    'for infants per 1 mL',
    NULL
  ),
  (
    '01248',
    'for children per 5 mL',
    NULL
  ),
  (
    '01249',
    '(equiv. to 10 mg elemental zinc)',
    NULL
  ),
  (
    '01250',
    '(equiv. to 30 mg elemental zinc)',
    NULL
  ),
  (
    '01251',
    '(equiv. to 10 mg elemental zinc/mL)',
    NULL
  ),
  (
    '01252',
    '(equiv. to 20 mg elemental zinc/5 mL)',
    NULL
  ),
  ('01254', '55 mg/5 mL', NULL),
  (
    '01255',
    '95% Concentration, 5 gallons',
    NULL
  ),
  ('01256', 'per 5 mL', NULL),
  (
    '01259',
    'equiv. to 600 mg elemental Ca + 400 IU Vit. D3',
    NULL
  ),
  ('01265', '250 mg/5 mL', NULL),
  ('01266', '1 kg', NULL),
  (
    '01267',
    'Equiv. to 500 mg elemental Ca + 200 IU Vit D3',
    NULL
  ),
  ('01268', '2%, 3.5 g', NULL),
  ('01269', 'per 1 mL ,15 mL', NULL),
  (
    '01270',
    '75 replacement 2.17 g',
    NULL
  ),
  (
    '01271',
    '75 replacement 5.28 g',
    NULL
  ),
  (
    '01272',
    '75 replacement 6.1 g',
    NULL
  ),
  ('01273', '20.5 g', NULL),
  (
    '01275',
    '600 mg elementa Ca + 400 IU Vit. D3',
    NULL
  ),
  (
    '01276',
    '2.5 IU/0.5 mL Vial + diluent',
    NULL
  ),
  ('01277', '75 mg/0.6 mL', NULL),
  (
    '01278',
    '600 mg elemental Ca + 200 IU Vit. D3',
    NULL
  ),
  ('01279', '4.1 g', NULL),
  (
    '01280',
    '60 mg elemental iron',
    NULL
  ),
  (
    '01282',
    '15 mg elemental iron/0.6 mL',
    NULL
  ),
  (
    '01283',
    '30 mg elemental iron/5 mL',
    NULL
  ),
  ('01285', '5000 IU/mL', NULL),
  ('01287', '275 mg', NULL),
  ('01288', '550 mg', NULL),
  (
    '01291',
    '10 mg elemental zinc/mL',
    NULL
  ),
  (
    '01292',
    '20 mg elemental zinc/5 mL',
    NULL
  ),
  ('01294', '250 mg powder', NULL),
  ('01295', '500 mg powder', NULL),
  ('01296', '150 mg powder', NULL),
  ('01297', '450 mg powder', NULL),
  ('01298', '10 mg powder', NULL),
  ('01299', '200 mg powder', NULL),
  ('01300', '1 g powder', NULL),
  (
    '01301',
    '2%, 1.5 g (medicated surgical dressing)',
    NULL
  ),
  ('01302', '6.1 g', NULL),
  (
    '01303',
    '2.5 IU/0.5 mL Vial +',
    NULL
  ),
  (
    '01306',
    '500 mg elemental Ca + 400 IU Vit. D3',
    NULL
  ),
  ('01309', '300 mL', NULL),
  ('01310', '400 mL', NULL),
  ('01311', '2%, 450 g', NULL),
  ('01312', '80 mcg', NULL),
  ('01314', '0.02', NULL),
  ('01315', '0.05', NULL),
  ('01316', '1.3 mg', NULL),
  ('01318', '0.025', NULL),
  ('01320', '0.09', NULL),
  (
    '01321',
    '1 g with 4% dextrose',
    NULL
  ),
  (
    '01322',
    '1 g with 3.74% dextrose',
    NULL
  ),
  ('01323', '5.125 g', NULL),
  ('01324', '3.35 g/5 mL', NULL),
  ('01326', '60 mg/mL', NULL),
  (
    '01331',
    '250 mg + 250 mg + 1000 mcg',
    NULL
  ),
  ('01332', '400 U Powder', NULL),
  ('01333', '0.0125', NULL),
  ('01334', '400 g', NULL),
  (
    '01335',
    '27.5 mg/mL (Equiv. to 10 mg Elemental Zinc)',
    NULL
  ),
  (
    '01336',
    '55 mg/5 mL (Equiv. to 20 mg Elemental Zinc)',
    NULL
  ),
  ('01337', '650 mg', NULL),
  (
    '01339',
    '100 IU/mL (Equiv. to 3.571 mg/mL)',
    NULL
  ),
  ('01340', '68 mg', NULL),
  ('01343', '2%, 2.5 g', NULL),
  (
    '01344',
    '70 mg/5 mL (Equiv. 10 mg Elemental Zinc)',
    NULL
  ),
  (
    '01346',
    '5 gallon (approx. 20 L)',
    NULL
  ),
  ('01347', '5 L', NULL),
  ('01348', '10 L', NULL),
  ('01350', '3 in 1 1400 Kcal', NULL),
  ('01351', '3 in 1 1000 Kcal', NULL),
  ('01353', '380 g', NULL),
  ('01354', '3 in 1 1900 Kcal', NULL),
  ('01355', '3 in 1 1300 Kcal', NULL),
  ('01356', '3 in 1 1500 Kcal', NULL),
  ('01357', '800 mg + 160 mg', NULL),
  ('01358', '1000 IU + 1 mL', NULL),
  ('01359', '500 IU + 1 mL', NULL),
  ('01360', '5000 IU + 1 mL', NULL),
  ('01361', '4 mg  + 2 mL', NULL),
  ('01362', '5 mg + 5 mL', NULL),
  ('01363', '1 g + 33 mL', NULL),
  ('01364', '10 g + 260 mL', NULL),
  ('01365', '2.5 g + 100 mL', NULL),
  ('01366', '5 g + 100 mL', NULL),
  (
    '01367',
    '15 mcg/0.5 mL  multidose',
    NULL
  ),
  (
    '01368',
    '125 mg/mL + diluent',
    NULL
  ),
  (
    '01369',
    '62.5 mg/mL  + diluent',
    NULL
  ),
  ('01370', 'Pedia', NULL),
  (
    '01371',
    '25 mg + 2 mL diluent',
    NULL
  ),
  (
    '01372',
    '37.5 mg  + 2 mL diluent',
    NULL
  ),
  (
    '01373',
    '1 g + 50 mL diluent',
    NULL
  ),
  ('01374', '150 mL', NULL),
  ('01375', '25 mL', NULL),
  ('01376', '4.5 g', NULL),
  ('01377', '180 mL', NULL),
  ('01378', '355 mL', NULL),
  ('01379', '22.5 mL', NULL),
  ('01380', '10 g', NULL),
  ('01381', '500 g', NULL),
  ('01382', '40 g', NULL),
  ('01383', '60 g', NULL),
  ('01384', '50 g', NULL),
  ('01385', '75 g', NULL),
  ('01386', '4 mL', NULL),
  ('01387', '120 Doses', NULL),
  ('01388', '60 Doses', NULL),
  (
    '01389',
    '4 mL (spinal) with 8% Dextrose',
    NULL
  ),
  ('01390', '45 mL', NULL),
  ('01391', '70 mL', NULL),
  ('01392', '7.5 mL', NULL),
  ('01393', '3.5 g', NULL),
  ('01394', '380 mL', NULL),
  ('01395', '4 L', NULL),
  ('01396', '0.4 mL', NULL),
  ('01397', '27 mL', NULL),
  ('01398', '450 g', NULL),
  ('01399', '130 ml', NULL),
  ('01400', '250 mL ', NULL),
  ('01401', '8 mL', NULL),
  ('01402', '7 mL', NULL),
  ('01403', '450 g ', NULL),
  ('01404', '0.2 mL', NULL),
  ('01405', '0.6 mL', NULL),
  ('01406', '0.8 mL', NULL),
  ('01407', '3 g', NULL),
  ('01408', '1 ml (unit dose)', NULL),
  ('01409', '2 ml (unit dose)', NULL),
  (
    '01410',
    '20 ml (multidose)',
    NULL
  ),
  ('01411', '35 g', NULL),
  (
    '01412',
    '2.5 ml (unit dose)',
    NULL
  ),
  (
    '01413',
    '10 ml (multidose)',
    NULL
  ),
  ('01414', '13.3 mL', NULL),
  ('01415', '20 g', NULL),
  ('01416', '0.85 mL', NULL),
  (
    '01417',
    '15 ml (multi dose)',
    NULL
  ),
  (
    '01418',
    '30 ml (multi dose)',
    NULL
  ),
  ('01419', '0.35 mL', NULL),
  ('01420', '0.45 ml', NULL),
  ('01421', '133 mL', NULL),
  ('01422', '66 mL', NULL),
  ('01423', '0.1 mL', NULL),
  ('01424', '0.3 mL', NULL),
  ('01425', '144 g', NULL),
  ('01426', '2.17 g', NULL),
  ('01427', '40 mL', NULL),
  ('01428', '16.7 mL', NULL),
  ('01429', '17 mL', NULL),
  ('01430', '43.4 mL', NULL),
  ('01431', '1.5 L', NULL),
  ('01432', '2 L', NULL),
  ('01433', '125 mL', NULL),
  (
    '01434',
    '2.5 ml  (multidose)',
    NULL
  ),
  ('01435', '1 Gallon', NULL),
  ('01436', '480 mL', NULL),
  ('01437', '5 mg + 10 mg', NULL),
  ('01438', '5 mg + 20 mg', NULL),
  ('01439', '5 mg + 40 mg', NULL),
  ('01440', '5 mg + 80 mg', NULL),
  ('01441', '10 mg + 10 mg', NULL),
  ('01442', '10 mg + 20 mg', NULL),
  ('01443', '10 mg + 40 mg', NULL),
  ('01444', '10 mg + 80 mg', NULL),
  ('01445', '1000 mg', NULL),
  (
    '01446',
    '150 mg + 30 mg + 20 mg',
    NULL
  ),
  (
    '01447',
    '187.5 mg + 50 mg + 25 mg/5 mL',
    NULL
  ),
  ('01448', '840 g', NULL),
  ('01449', '440 g', NULL),
  ('01450', '845 g', NULL),
  ('01451', '1.2 g', NULL),
  (
    '01452',
    '125 mg + 31.25 mg/5 mL',
    NULL
  ),
  ('01453', '50 mg/2 mL', NULL),
  ('01454', '5 mg + 50 mg', NULL),
  (
    '01455',
    '3,800 I.U. / 0.4 mL',
    NULL
  ),
  ('01456', '0.3% x 5 mL', NULL),
  (
    '01457',
    '150 mcg/mL x 15 mL ',
    NULL
  ),
  ('01458', '7.5 mg', NULL),
  (
    '01459',
    '25 mcg + 50 mcg metered dose inhaler + dose Counter',
    NULL
  ),
  (
    '01460',
    '25 mcg + 125 mcg metered dose inhaler + dose Counter',
    NULL
  ),
  (
    '01461',
    '25 mcg + 250 mcg metered dose inhaler + dose Counter',
    NULL
  ),
  ('01462', '0.084', NULL),
  ('01463', '35 mL', NULL),
  (
    '01464',
    '9 million IU, 1 mL',
    NULL
  ),
  ('01465', '100 mg + 12.5 mg', NULL) ;

