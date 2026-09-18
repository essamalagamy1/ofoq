import json
from collections import Counter

try:
    with open("phpstan_errors_filtered.json", "r") as f:
        data = json.load(f)

    files_count = len(data["files"])
    print(f"Total files with errors: {files_count}")
    
    error_messages = []
    identifiers = []
    for file, info in data["files"].items():
        for error in info["messages"]:
            msg = error["message"].split(" in ")[0].split(" of ")[0]
            if "has parameter" in msg and "with no value type specified" in msg:
                msg = "Method has parameter with no value type specified in iterable type"
            if "return type has no value type specified" in msg:
                msg = "Method return type has no value type specified in iterable type"
            if "has no value type specified in iterable type" in msg:
                msg = "Property has no value type specified in iterable type"
            error_messages.append(msg)
            if "identifier" in error:
                identifiers.append(error["identifier"])

    print("\nTop 15 error messages:")
    for msg, count in Counter(error_messages).most_common(15):
        print(f"[{count}] {msg}")

    print("\nTop 15 error identifiers:")
    for ident, count in Counter(identifiers).most_common(15):
        print(f"[{count}] {ident}")

except Exception as e:
    print(f"Error: {e}")
