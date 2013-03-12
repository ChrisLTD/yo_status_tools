# YO Status Tools
YO Status Tools will let you change the status of a channel entry, delete old entries by status, or read a status from a template.

## Changing Status
This will change the status of an entry. In the parameters for the tag you need to supply either a url_title or entry_id and a new_status.

```php
{exp:yo_status_tools:change_entry_status url_title="URL_TITLE_HERE" new_status="closed"}

{exp:yo_status_tools:change_entry_status entry_id="ENTRY_ID_HERE" new_status="open"}
```

## Delete Old Entries
This will delete the entries that match the status, channel and are older than the age_in_seconds value you provide. In the parameters for the tag you need to supply the channel_name, an age_in_seconds, and status.

```php
{exp:yo_status_tools:delete_old_entries channel_name="CHANNEL_NAME" age_in_seconds="2628000" status="closed"}
```

## Read Status
This will output a channel entry's status as a string. In the parameters for the tag you need to supply either a url_title or entry_id.

```php
{exp:yo_status_tools:read_entry_status url_title="URL_TITLE_HERE"}

{exp:yo_status_tools:read_entry_status entry_id="ENTRY_ID_HERE"}
```

## Installation
Put the yo_status_tools folder inside system/expressionengine/third_party/