<?php

function invoke_is_down(): bool
{
    return file_exists(INVOKE_MAINTENANCE_FILE_PATH);
}

function invoke_down()
{
    if (!touch(INVOKE_MAINTENANCE_FILE_PATH)) {
        $message = sprintf(
            "Something went wrong on trying to create maintenance file %s.",
            INVOKE_MAINTENANCE_FILE_PATH,
        );

        throw new RuntimeException($message);
    }
}

function invoke_up()
{
    if (file_exists(INVOKE_MAINTENANCE_FILE_PATH) && !unlink(INVOKE_MAINTENANCE_FILE_PATH)) {
        $message = sprintf(
            "Something went wrong on trying to remove maintenance file %s.",
            INVOKE_MAINTENANCE_FILE_PATH
        );

        throw new RuntimeException($message);
    }
}
