import json
with open("phpstan_errors_filtered.json", "r") as f:
    data = json.load(f)
print("Total files with errors:", len(data["files"]))
errors = sum(len(info["messages"]) for info in data["files"].values())
print("Total errors:", errors)
