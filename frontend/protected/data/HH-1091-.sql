INSERT INTO
    `seg_rep_templates_registry` (
        `report_id`,
        `rep_group`,
        `rep_name`,
        `rep_description`,
        `rep_script`,
        `rep_dept_nr`,
        `rep_category`,
        `is_active`,
        `with_template`,
        `query_in_jasper`,
        `template_name`,
        `exclusive_opd_er`,
        `exclusive_death`
    )
VALUES
    (
        'Billing_Transmittal_Based_On_PHIC_Category',
        'Billing Report',
        'Total no. ',
        'Type of Deaths',
        'Type_Deaths',
        '151',
        'HOSP',
        '1',
        '1',
        '0',
        'MR_Type_Deaths',
        '0',
        '0'
    );

UPDATE
    `seg_rep_templates_registry`
SET
    `template_name` = 'Billing_Transmittal_Based_On_PHIC_Category'
WHERE
    `report_id` = 'Billing_Transmittal_Based_On_PHIC_Category';

UPDATE
    `seg_rep_templates_registry`
SET
    `rep_script` = 'Billing_Transmittal_Based_On_PHIC_Category'
WHERE
    `report_id` = 'Billing_Transmittal_Based_On_PHIC_Category';

UPDATE
    `seg_rep_templates_registry`
SET
    `rep_description` = 'Total no. claims'
WHERE
    `report_id` = 'Billing_Transmittal_Based_On_PHIC_Category';

UPDATE
    `seg_rep_templates_registry`
SET
    `rep_name` = 'Total No. of Transmittal Based On PhilHealth Category',
    `rep_description` = 'Total No. of Transmittal Based On PhilHealth Category'
WHERE
    `report_id` = 'Billing_Transmittal_Based_On_PHIC_Category';

INSERT INTO
    `gmmci4dev_040721`.`seg_signatory_document` (`document_code`, `document_name`)
VALUES
    ('billing_report', 'Billing Report');

INSERT INTO
    `gmmci4dev_040721`.`seg_signatory` (
        `personell_nr`,
        `signatory_position`,
        `signatory_title`,
        `document_code`
    )
VALUES
    (
        '100421',
        'Hospital Administrator',
        'Hospital Administrator',
        'billing_report'
    );