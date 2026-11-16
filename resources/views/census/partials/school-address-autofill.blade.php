<script>
    function fillCensusLocationFromSchool(school, locationTree, provinceSelect, districtSelect, communeSelect, villageSelect) {
        let location = school.location;

        if (!location && school.address) {
            const addressParts = school.address.split(',').map(part => part.trim().toLocaleLowerCase());
            for (const province of locationTree) {
                for (const district of province.districts) {
                    for (const commune of district.communes) {
                        const village = commune.villages.find(item =>
                            item.name.trim().toLocaleLowerCase() === addressParts[0]
                        );
                        if (
                            village &&
                            commune.name.trim().toLocaleLowerCase() === addressParts[1] &&
                            district.name.trim().toLocaleLowerCase() === addressParts[2] &&
                            province.name.trim().toLocaleLowerCase() === addressParts[3]
                        ) {
                            location = {
                                province_id: province.id,
                                district_id: district.id,
                                commune_id: commune.id,
                                village_id: village.id,
                            };
                            break;
                        }
                    }
                    if (location) break;
                }
                if (location) break;
            }
        }

        if (!location) return false;

        const province = locationTree.find(item => String(item.id) === String(location.province_id));
        const district = province?.districts.find(item => String(item.id) === String(location.district_id));
        const commune = district?.communes.find(item => String(item.id) === String(location.commune_id));
        if (!province || !district || !commune) return false;

        const setOptions = (select, items, selectedId) => {
            const placeholder = select.options[0]?.text ?? '';
            select.replaceChildren(new Option(placeholder, ''));
            items.forEach(item => select.add(new Option(item.name, item.id)));
            select.value = String(selectedId);
        };

        provinceSelect.value = String(province.id);
        setOptions(districtSelect, province.districts, district.id);
        setOptions(communeSelect, district.communes, commune.id);
        setOptions(villageSelect, commune.villages, location.village_id);

        return true;
    }
</script>
