<script id="dependent-template" type="mustache_template">
    <tr>
        <td>
            <select class="relation" name="Dependents[relation][]" title="Relation">
                <option value="c">Child</option>
                <option value="s">Spouse</option>
                <option value="f">Father</option>
                <option value="m">Mother</option>
            </select>
        </td>
        <td>
            <input class="pin" type="text" name="Dependents[pin][]" title="PIN" value="{{dependent_pin}}"/>
        </td>
        <td>
            <input class="first_name" type="text" name="Dependents[first_name][]" title="First Name" value="{{dependent_first_name}}"/>
        </td>
        <td>
            <input class="middle_name" type="text" name="Dependents[middle_name][]" title="Middle Name" value="{{dependent_middle_name}}"/>
        </td>
        <td>
            <input class="last_name" type="text" name="Dependents[last_name][]" title="Last Name" value="{{dependent_last_name}}"/>
        </td>
        <td>
            <input class="name_extension" type="text" name="Dependents[name_extension][]" title="Name Suffix" value="{{dependent_name_extension}}"/>
        </td>
        <td>
            <input class="birth_date calendar" type="text" name="Dependents[birth_date][]" title="Birth Date (mm/dd/yyyy)" value="{{dependent_birth_date}}"/>
        </td>
        <td>
            <select class="sex" name="Dependents[sex][]" title="Sex">
                <option value="m">Male</option>
                <option value="f">Female</option>
            </select>
        </td>
        <td>
            <select class="is_disabled" name="Dependents[is_disabled][]" title="Is Disabled?">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </td>
        <td class="cell-button">
            <a class="delete-button" style="font-size: x-large;color: #000000;">
                <i class="fa fa-times-circle"></i>
            </a>
        </td>
    </tr>
</script>