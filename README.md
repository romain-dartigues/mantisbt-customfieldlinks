# README

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/b5f8d541fb9449deae572e480bff7a07)](https://www.codacy.com/app/romain-dartigues/mantisbt-customfieldslinks?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=romain-dartigues/mantisbt-customfieldslinks&amp;utm_campaign=Badge_Grade)

This [MantisBT] [plug-in](https://github.com/mantisbt-plugins/) is an attempt to provide custom links for custom fields.

[MantisBT]: https://github.com/mantisbt/mantisbt

## Use case

Say you want to be able to link an issue in your Mantis to another Mantis and another tool (Atlassian Jira®, GitHub®, … whatever).

1. you [create][ccf] two new [custom fields], for example: `Jira_project`, `GitHub_project_issue` and `OtherMantis_project`
2. set them as "string type", with a regular expression to use for validating user input (examples: `(\w+)` or `(\d+)`); this part is important because the regex will be used for data extraction by the plug-in
3. configure this plug-in through the administration interface, for example:
```
Jira_project = https://jira.example.net/browse/%s
GitHub_project_issue = https://github.com/project-group/project/issues/%d
OtherMantis_project = https://mantisbt.example.net/view.php?id=%d
```

That's it, now your custom fields will be links.

[custom fields]: https://www.mantisbt.org/docs/master/en-US/Admin_Guide/html/admin.customize.customfields.definitions.html
[ccf]: https://www.mantisbt.org/docs/master/en-US/Admin_Guide/html/admin.customize.customfields.editing.html

## Notes

I quickly made this plug-in to answer some internal needs, to replace the legacy solution which was an ugly patch against `core/custom_field_api.php` `string_custom_field_value()`, and lastly because I haven't found a more appropriate way to do it.

I wouldn't had needed to go to such hackish lengths if MantisBT bug / feature-request [#13180] (Url template custom field) would have been properly implemented to the core.

[#13180]: https://www.mantisbt.org/bugs/view.php?id=13180

If you know a more sensible way to implement this, by any means, [let me know](https://github.com/romain-dartigues/mantisbt-customfieldslinks/issues/new).
