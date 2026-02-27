<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('hr.partials.sidebar')
            <section class="p-4 sm:p-6 lg:p-8">
                <div class="bg-white p-6 shadow rounded-xl border border-gray-200">
                        <h1 class="text-2xl font-semibold text-gray-800 mb-1">Help & Support</h1>
                        <p class="text-sm text-gray-500 mb-6">Guides for HR users and information about the system.</p>

                        <div class="space-y-8">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">Help</h2>
                                <div class="mt-2 space-y-2 text-gray-700">
                                    <p class="text-sm">How to use the HR module:</p>
                                    <ul class="list-disc list-inside text-sm space-y-1">
                                        <li>Open Leave Requests from the sidebar to see all submissions.</li>
                                        <li>Use the chips (All, Pending, Approved, Rejected) and the search box to filter results.</li>
                                        <li>Click View on a row to open the request.</li>
                                        <li>In the request view, navigate using tabs:
                                            <span class="font-medium">Leave Request</span>, <span class="font-medium">Certification of Leave Credits</span>,
                                            <span class="font-medium">Recommendation</span>, and <span class="font-medium">Approved For / Disapproved Due To</span>.
                                        </li>
                                        <li>Fill out the table under Certification of Leave Credits; balances auto-compute.</li>
                                        <li>Choose a recommendation (For approval / For disapproval due to) and add remarks if needed.</li>
                                        <li>Finalize the decision under Approved For / Disapproved Due To and update the status.</li>
                                    </ul>
                                </div>
                            </div>

                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">Support</h2>
                                <div class="mt-2 text-gray-700 text-sm space-y-3">
                                    <h3 class="font-medium">About the System</h3>
                                    <p>This system was developed by On-the-Job Training (OJT) students from Universidad de Dagupan in partnership with Department of the Interior and Local Government - Cordillera Administrative Region (DILG CAR).</p>
                                    <p>The project aims to support and enhance operational efficiency, improve information management, and provide a reliable digital solution tailored to the needs of DILG CAR.</p>
                                    <p>This initiative reflects the students’ commitment to applying academic knowledge to real-world government systems and contributing to public service innovation.</p>

                                    <h3 class="font-medium">OJT Details</h3>
                                    <ul class="list-disc list-inside">
                                        <li>Institution: Universidad de Dagupan</li>
                                        <li>Deployment: Department of the Interior and Local Government – Cordillera Administrative Region (DILG CAR)</li>
                                        <li>Mentor: Sir Billy John Ferreol</li>
                                    </ul>

                                    <h3 class="font-medium">Development Team</h3>
                                    <ul class="list-disc list-inside">
                                        <li>Patrick Medrano</li>
                                        <li>Kevin Aquino</li>
                                        <li>Kathleen Charm Daroy</li>
                                        <li>Mark Ezekiel Zareno</li>
                                        <li>Rahm Soriano</li>
                                        <li>Josiah Carrera</li>
                                    </ul>

                                    <h3 class="font-medium">Support</h3>
                                    <p>For technical assistance, system concerns, or inquiries, please contact the development team through your assigned system administrator or directly coordinate with the DILG CAR office.</p>
                                    <p>We are committed to ensuring system reliability, continuous improvement, and user satisfaction.</p>
                                </div>
                            </div>
                        </div>
                    </div>
            </section>
        </div>
    </div>
</x-app-layout>

